<div class="alert">
    <div class="alert-error">
        <strong>Внимание!</strong> Невозможно удалить комнату, так как имеются связанные события:
        <details>
            <summary>Список событий</summary>
            <ul class="mt-1">
                @foreach($events as $event)
                    <li>[{{ $event->id }}]
                        {{--                TODO  Добавить ссылки на страницу админки для редактирования событий --}}
                        {{$event->name}}
                        (Пользователя:
                        <a href="{{route('platform.systems.users.edit', $event->user)}}"> {{$event->user->email}})</a>
                    </li>
                @endforeach
            </ul>
        </details>
    </div>
    <hr>
    <div class=" alert alert-info">
        <strong>Инструкция:</strong>
        <ol class="mb-0 ml-4">
            <li>Нажмите кнопку "Восстановить" для восстановления комнаты</li>
            <li>Свяжитесь с владельцами событий (см. список выше)</li>
            <li>Удалите или перенесите события в другую комнату</li>
            <li>Повторите попытку окончательного удаления</li>
        </ol>
    </div>


</div>



