<tr class="center-line">
    <th class="col-medium">{{ $task->name }}</th>
    <td class="col-large">{{ $task->description }}</td>
    <td class="col-small text-center">
        <select class="form-control" name="frequency" form="schedule_form">
            @foreach ($task->frequencies as $item)
                <option {{ $task->isSelected($item) ? 'selected' : '' }}>{{ $item }}</option>
            @endforeach
        </select>
    </td>
    <td class="col-small text-center">
        <input class="form-control" type="time" name="starting" form="schedule_form" />
    </td>
    <td class="col-small text-center">
        <button class="btn btn-secondary"
                type="submit"
                name="name"
                value="bct:main_boards_producer"
                form="schedule_form">
            <i class="far fa-clock"></i>
        </button>
    </td>
</tr>