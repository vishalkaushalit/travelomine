<div class="card mt-3">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title">
            <i class="fas fa-plane mr-2"></i>Flight Details
        </h3>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="flighttype">Flight Type <span class="text-danger">*</span></label>
                    <select class="form-control" id="flighttype" name="flight_type">
                        <option value="">-- Select Flight Type --</option>
                        <option value="oneway" {{ old('flight_type') == 'oneway' ? 'selected' : '' }}>One Way</option>
                        <option value="roundtrip" {{ old('flight_type') == 'roundtrip' ? 'selected' : '' }}>Round Trip</option>
                        <option value="multicity" {{ old('flight_type') == 'multicity' ? 'selected' : '' }}>Multi City</option>
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="gkpnr">GK PNR</label>
                    <input type="text" class="form-control" id="gkpnr" name="gk_pnr" value="{{ old('gk_pnr') }}">
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="airlinepnr">Airline PNR</label>
                    <input type="text" class="form-control" id="airlinepnr" name="airline_pnr" value="{{ old('airline_pnr') }}">
                </div>
            </div>
        </div>

        <div id="flighttypehint" class="alert alert-info">
            Please select a flight type to add flight segment details.
        </div>

        <div id="segmentscontainer" style="display:none;"></div>

        <div id="addsegmentwrapper" style="display:none;" class="mt-2">
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addSegment()">
                <i class="fas fa-plus mr-1"></i> Add Segment
            </button>
        </div>
    </div>
</div>
