<head>
</head>
<div class="container" style="display: flex; flex-direction: column; gap: 30px; align-items: center;">
    <div class="col-12 mb-4 text-end">
        <button id="showFormBtn" class="btn btn-primary px-4 py-2 btn-shadow frombtnn">
            <i class="fas fa-plus-circle me-2"></i> Add Others
        </button>
    </div>
    <div id="addInjuryForm"
        style="display: none; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1); border-radius: 8px; background-color: #fff; width: 100%; max-width: 500px; padding: 30px;">
        <table class="table table-striped table-nomargin table-mail shownbtnn">
            <tbody>
                <tr>
                    <td style="display: flex; flex-direction: row; align-items: center;">
                        <input type="text" class="typ form-control" name="name" placeholder="Enter Injury name"
                            style="margin-right: 10px; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        <input type="hidden" class="cat" name="cat" value="99">
                        <input type="hidden" class="active_status_id" name="active_status_id" value="1">
                        <input type="button" class="btnsysadd" value="Add" style="
                background-color: #007bff; 
                color: white; 
                border: none; 
                border-radius: 4px; 
                padding: 10px 15px; 
                cursor: pointer; 
                transition: background-color 0.3s;">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <table class="table table-striped table-nomargin table-mail"
        style="box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1); border-radius: 8px; background-color: #fff; width: 100%; max-width: 900px;">
        <thead>
            <tr>
                <th class="sys text-center" style="width: 10%;">#</th>
                <th class="sys" style="width: 50%; text-align: left; font-weight: bold; color: #333;">Injury Type</th>
                <th class="sys text-center" style="width: 20%; font-weight: bold; color: #333;">Type</th>
                <th class="sys text-center" style="width: 20%; font-weight: bold; color: #333;">Edit</th>
            </tr>
        </thead>
        <tbody id="injury-list" class="inj">
            @foreach ($injuries as $index => $injury)
            <tr class="uniqueinjury" style="transition: background-color 0.3s; cursor: pointer;"
                onmouseover="this.style.backgroundColor='#f2f2f2'"
                onmouseout="this.style.backgroundColor='transparent'">
                <td class="text-center" style="padding: 15px;">{{ $index + 1 }}</td>
                <td class="hid" style="text-align: left; padding: 10px;">
                    <input type="text" class="mdinjury form-control" value="{{ $injury['op_component_name'] }}"
                        style="padding: 10px; width: 100%; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
                </td>
                <td class="text-center" style="padding: 10px;">
                    <input type="text" class="sta form-control"
                        style="width: 80px; padding: 8px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;"
                        value="{{ $injury['op_component_type'] }}" readonly>
                </td>
                <td class="text-center" style="padding: 10px;">
                    <div class="btn-group"
                        style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                        <input type="hidden" value="{{ $injury['op_component_id'] }}" class="op_component_id">
                        <input type="hidden" value="{{ $injury['op_component_type'] }}" class="op_component_type">
                        <input type="hidden" value="1" class="active_status">
                        <input type="button" class="btnupsys btn-success" value="Update" style="padding: 10px 20px; background-color: #28a745; color: white; border: none;
                      border-radius: 6px; font-size: 14px; cursor: pointer; transition: background-color 0.3s, 
                      transform 0.3s; width: 100%; max-width: 150px; text-align: center; display: inline-flex; 
                      justify-content: center; align-items: center; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                        <style>
                            .btnupsys:hover {
                                background-color: #218838;
                                transform: translateY(-2px);
                            }

                            .btnupsys:focus {
                                outline: none;
                                box-shadow: 0 0 5px rgba(40, 167, 69, 0.7);
                            }
                        </style>
                        <form action="{{ route('injurydelete', $injury['op_component_id']) }}" method="POST"
                            style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" style="padding: 10px 20px; border-radius: 6px; border: none; background-color: #dc3545; 
                    color: white; font-size: 16px; cursor: pointer; transition: background-color 0.3s, transform 0.3s;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                                <i class="fas fa-trash-alt" style="font-size: 18px; margin-right: 8px;"></i>
                                Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center mt-3">
        {!! $injuries->links('pagination::bootstrap-5') !!}
    </div>
</div>
<script src="/lib/js/page-scripts/injury_9.js"></script>