import { useState } from 'react';

export default function usePopupPosition() {
    const [popupPosition, setPopupPosition] = useState({ x: 0, y: 0, offset: 0 });
    const [isPopupOpen, setIsPopupOpen] = useState(false);

    const handleEventClick = (info: any) => {
        const target = info.jsEvent.target as HTMLElement;
        if (target) {
            const rect = target.getBoundingClientRect();
            const viewportWidth = window.innerWidth;
            const popupWidth = 300; // Ширина попапа

            // Вычисляем смещение в зависимости от позиции события
            const distanceFromRight = viewportWidth - rect.right;
            const maxOffset = popupWidth * 0.7;
            const offset = Math.min(maxOffset, Math.max(0, popupWidth - distanceFromRight));

            setPopupPosition({
                x: rect.left,
                y: rect.top,
                offset: offset
            });
            setIsPopupOpen(true);
        }
    };

    return {
        popupPosition,
        isPopupOpen,
        handleEventClick,
        setIsPopupOpen
    };
} 