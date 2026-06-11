import {
    Switch,
    Table,
    TableColumnConfig,
    Tooltip,
    withTableSelection,
    withTableSettings,
    withTableSorting
} from '@gravity-ui/uikit';
import React, {ReactElement, useCallback, useEffect, useMemo, useState} from 'react';
import {router} from "@inertiajs/react";
import {AsyncStatusLabel} from "@/Components/Room/StatusLabel";

// ==================== Утилиты для localStorage (глобальные) ====================

const GLOBAL_STORAGE_KEY = 'tableSettings_global';
const ADAPTIVE_MODE_KEY = 'tableSettings_adaptiveMode';

const loadSettingsFromStorage = (): Array<{ id: string; isSelected?: boolean }> | null => {
    try {
        const saved = localStorage.getItem(GLOBAL_STORAGE_KEY);
        if (saved) {
            const parsed = JSON.parse(saved);
            const columnIds = ['id', 'name', 'capacity', 'status', 'workload', 'description'];
            if (Array.isArray(parsed) && parsed.every((s: any) => columnIds.includes(s.id))) {
                return parsed.map((s: any) => ({id: s.id, isSelected: s.isSelected ?? true}));
            }
        }
    } catch (error) {
        console.warn('[TableSettings] Ошибка чтения настроек из localStorage:', error);
    }
    return null;
};

const saveSettingsToStorage = (settings: Array<{ id: string; isSelected?: boolean }>) => {
    try {
        localStorage.setItem(GLOBAL_STORAGE_KEY, JSON.stringify(settings));
    } catch (error) {
        console.warn('[TableSettings] Ошибка записи настроек в localStorage:', error);
    }
};

const loadAdaptiveMode = (): boolean => {
    try {
        const saved = localStorage.getItem(ADAPTIVE_MODE_KEY);
        return saved !== null ? saved === 'true' : true; // по умолчанию включён
    } catch (error) {
        console.warn('[TableSettings] Ошибка чтения режима из localStorage:', error);
        return true;
    }
};

const saveAdaptiveMode = (value: boolean) => {
    try {
        localStorage.setItem(ADAPTIVE_MODE_KEY, String(value));
    } catch (error) {
        console.warn('[TableSettings] Ошибка записи режима в localStorage:', error);
    }
};

// ==================== Хук для определения размера экрана ====================

const getScreenSize = (): 'mobile' | 'tablet' | 'desktop' => {
    if (typeof window === 'undefined') {
        return 'desktop';
    }
    const width = window.innerWidth;
    if (width < 576) return 'mobile';
    if (width < 992) return 'tablet';
    return 'desktop';
};

// Дефолтные настройки в зависимости от размера экрана
const getDefaultSettingsForScreen = (screenSize: 'mobile' | 'tablet' | 'desktop') => {
    const base: Array<{ id: string; isSelected: boolean }> = [
        {id: 'id', isSelected: false},
        {id: 'name', isSelected: false},
        {id: 'capacity', isSelected: false},
        {id: 'status', isSelected: false},
        {id: 'workload', isSelected: false},
        {id: 'description', isSelected: false},
    ];

    switch (screenSize) {
        case 'mobile':
            base[1].isSelected = true;  // name
            base[3].isSelected = true;  // status
            break;
        case 'tablet':
            base[1].isSelected = true;  // name
            base[2].isSelected = true;  // capacity
            base[3].isSelected = true;  // status
            base[4].isSelected = true;  // workload
            break;
        case 'desktop':
            base.forEach(s => s.isSelected = true);
            break;
    }

    return base;
};

const ADAPTIVE_CONTROL_ID = '__adaptive__';


interface TableRoomsProps {
    officeId: number | bigint,
    rooms?: RoomType[],
    onUpdate?: (roomsId: (string[] | number[])) => void
}

export interface RoomType {
    id: number;
    name?: string;
    capacity?: number;
    description?: string | null;
    office_id?: number;
    created_at?: string;
    updated_at?: string;
    status: string;
    workload: number;
    booked_events?: number;
}


export default function TableRooms({officeId, rooms, onUpdate}: TableRoomsProps): ReactElement {
    // 1. Композиция HOC-ов
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const TableWithSelection = withTableSelection<any>(Table) as any;
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const TableWithSorting = withTableSorting(TableWithSelection) as any;
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const MyTable = withTableSettings()(TableWithSorting) as any;

    const [selectedIds, setSelectedIds] = useState<string[]>([]);

    // Адаптивный режим (глобальный)
    const [isAdaptiveMode, setIsAdaptiveMode] = useState(loadAdaptiveMode);

    // Инициализация настроек
    const getInitialSettings = (): Array<{ id: string; isSelected?: boolean }> => {
        const saved = loadSettingsFromStorage();
        if (saved) return saved;
        return getDefaultSettingsForScreen(getScreenSize());
    };

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const [settings, setSettings] = useState<any>(getInitialSettings);
    const [screenSize, setScreenSize] = useState(getScreenSize);

    // Отслеживаем изменение размера экрана
    useEffect(() => {
        let timeoutId: ReturnType<typeof setTimeout>;

        const handleResize = () => {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                const newScreenSize = getScreenSize();
                setScreenSize(newScreenSize);

                // Применяем адаптивные настройки только если адаптивный режим включён
                if (isAdaptiveMode) {
                    const defaultForCurrentScreen = getDefaultSettingsForScreen(newScreenSize);
                    // Преобразуем в формат с ADAPTIVE_CONTROL_ID
                    const withAdaptive = [
                        {id: ADAPTIVE_CONTROL_ID, isSelected: true},
                        ...defaultForCurrentScreen
                    ];
                    setSettings(withAdaptive);
                }
            }, 150);
        };

        window.addEventListener('resize', handleResize);
        return () => {
            window.removeEventListener('resize', handleResize);
            clearTimeout(timeoutId);
        };
    }, [isAdaptiveMode]);

    // Обработчик обновления настроек
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const handleUpdateSettings = useCallback((newSettings: any) => {
        // Проверяем: была ли изменена обычная колонка (не адаптивный чекбокс)
        const columnIds = ['id', 'name', 'capacity', 'status', 'workload', 'description'];
        const changedColumns = newSettings.filter((s: any) => columnIds.includes(s.id));
        const wasManualChange = changedColumns.some((s: any) => s.isSelected !== undefined);

        // Если была ручная настройка колонок — отключаем адаптивный режим
        if (wasManualChange && isAdaptiveMode) {
            setIsAdaptiveMode(false);
            saveAdaptiveMode(false);
        }

        // Фильтруем settings: сохраняем только реальные колонки (без __adaptive__)
        const filteredSettings = newSettings.filter((s: any) => columnIds.includes(s.id));

        saveSettingsToStorage(filteredSettings);
        setSettings(newSettings);
        return Promise.resolve();
    }, [isAdaptiveMode]);

    // Кастомный рендер контролов с чекбоксом "Адаптивный"
    const renderControls = useMemo(() => {
        return (params: {
            DefaultApplyButton: React.ComponentType;
            onApply: () => void;
        }) => {
            const {DefaultApplyButton, onApply} = params;

            return (
                <div style={{
                    display: 'flex',
                    alignItems: 'center',
                    gap: '12px',
                    padding: '8px 0',
                    flexWrap: 'wrap'
                }}>
                    {/* Switch "Адаптивный" */}
                    <label style={{
                        display: 'flex',
                        alignItems: 'center',
                        gap: '6px',
                        cursor: 'pointer',
                        fontWeight: isAdaptiveMode ? 600 : 400,
                        color: isAdaptiveMode ? 'var(--g-color-text-primary)' : 'var(--g-color-text-secondary)',
                        userSelect: 'none',
                        padding: '4px 8px',
                        borderRadius: '4px',
                        background: isAdaptiveMode ? 'var(--g-color-base-brand-alpha)' : 'transparent',
                        transition: 'all 0.15s ease'
                    }}>
                        <Switch
                            checked={isAdaptiveMode}
                            onChange={() => {
                                const checked = !isAdaptiveMode;
                                setIsAdaptiveMode(checked);
                                saveAdaptiveMode(checked);

                                if (checked) {
                                    const defaultForScreen = getDefaultSettingsForScreen(screenSize);
                                    const withAdaptive = [
                                        {id: ADAPTIVE_CONTROL_ID, isSelected: true},
                                        ...defaultForScreen
                                    ];
                                    handleUpdateSettings(withAdaptive);
                                }
                            }}
                        />
                        <span>🔄 Адаптивный</span>
                    </label>

                    {/* Стандартная кнопка "Применить" */}
                    <DefaultApplyButton/>
                </div>
            );
        };
    }, [isAdaptiveMode, screenSize, handleUpdateSettings]);


    const data = rooms ?? [];
    const noData = officeId === 0 || data.length === 0 ? 'No data' : '';

    const getOccupancyLevel = (rate: number) => {
        if (rate <= 20) return {icon: '🟢', label: 'Низкая'};
        if (rate <= 50) return {icon: '🟡', label: 'Средняя'};
        if (rate <= 70) return {icon: '🟠', label: 'Высокая'};
        return {icon: '🔴', label: 'Очень высокая'};
    };

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const columns: any = [
        {id: 'id', meta: {sort: true}},
        {id: 'name', name: 'Название', meta: {sort: true}},
        {id: 'capacity', name: 'Вместимость', meta: {sort: true}},
        {
            id: 'status',
            name: 'Статус',
            meta: {sort: true},
            template: (item: RoomType) => (<AsyncStatusLabel room={item}/>)
        },
        {
            id: 'workload',
            name: 'Загруженность',
            align: 'center',
            meta: {sort: true},
            template: (item: RoomType) => {
                const workload = item.workload ?? 0;
                const occupancy = getOccupancyLevel(workload);
                return (
                    <Tooltip content={`${occupancy.label} загрузка (${workload}%)`}>
                        <span>{occupancy.icon}</span>
                    </Tooltip>
                );
            }
        },
        {id: 'description', name: "Описание"}
    ];

    const getRowId = 'id';

    function changeSelectedIds(ids: string[]) {
        setSelectedIds(ids);
        if (onUpdate) {
            onUpdate(ids);
        }
    }

    return (
        <MyTable
            data={data}
            columns={columns}
            getRowId={getRowId}
            selectedIds={selectedIds}
            onSelectionChange={changeSelectedIds}
            emptyMessage={noData}
            edgePadding={true}
            // eslint-disable-next-line @typescript-eslint/no-explicit-any
            onRowClick={(room: any) => {
                router.visit(`/offices/${room.office_id}/rooms/${room.id}`);
            }}
            // 3. Передаем пропсы для HOC withTableSettings
            settings={settings}
            updateSettings={handleUpdateSettings}
            renderControls={renderControls}
        />
    );
}
