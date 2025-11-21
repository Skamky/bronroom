<div class="alert-content">
    <p class="mb-3">Невозможно удалить офис из-за связанных комнат:</p>

    @if(!empty($active_rooms))
        <div class="mb-4">
            <h6 class="font-semibold text-green-600 mb-2">🟢 Активные комнаты ({{ count($active_rooms) }})</h6>
            <p class="text-sm text-gray-600 mb-2">Смените офис у этих комнат для удаления офиса:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($active_rooms as $id => $room)
                    <li>
                        [{{ $id }}]
                        <a href="{{ route('platform.room.edit', $id) }}" class="text-primary hover:underline">
                            {{ $room }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(!empty($trashed_rooms))
        <div>
            <h6 class="font-semibold text-red-600 mb-2">🔴 Удаленные комнаты ({{ count($trashed_rooms) }})</h6>
            <p class="text-sm text-gray-600 mb-2">Исправьте эти комнаты для удаления офиса:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($trashed_rooms as $id => $room)
                    <li>
                        [{{ $id }}]
                        <span class="text-gray-500">{{ $room }}</span>
                        <a href="{{ route('platform.room.edit', $id) }}" class="text-orange-500 hover:underline ml-2">
                            (управление)
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-4 p-3 bg-gray-100 rounded text-sm">
        <strong>Решение:</strong>
        @if(!empty($active_rooms) && !empty($trashed_rooms))
            Для активных комнат смените офис.
            <br>
            Для удаленных - окончательно удалите комнату или восстановите комнату и смените их офис.
        @elseif(!empty($active_rooms))
            Смените офис у активных комнат.
        @else
            Окончательно удалите комнаты или восстановите комнату и смените их офис.
        @endif
    </div>
</div>
{{--TODO сменить текстовку для удаленных комнат и заменить ссылку на страницу с удаленными комнатами--}}
