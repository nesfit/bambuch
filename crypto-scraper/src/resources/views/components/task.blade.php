<form method="post" id="{{ $task->name }}" action="/scheduler/make">
    @csrf
    <table class="table table-light mb-0 hidden-overflow-table">
        <tbody>
        <tr class="center-line">
            <th class="col-medium">{{ $task->name }}</th>
            <td class="col-large">{{ $task->description }}</td>
            <td class="col-small text-center">
                <select class="form-control" name="frequency">
                    @foreach ($task->frequencies as $item)
                        <option {{ $task->isSelected($item) ? 'selected' : '' }}>{{ $item }}</option>
                    @endforeach
                </select>
            </td>
            <td class="col-small text-center">
                <input 
                    class="form-control"
                    type="time"
                    name="starting"
                    value="{{ $task->starting }}"
                />
            </td>
            <td class="col-small text-center">
                <button class="btn btn-secondary"
                        type="submit"
                        name="name"
                        value="{{ $task->name }}">
                    <i class="far fa-clock"></i>
                </button>
            </td>
        </tr>
        </tbody>
    </table>
</form>