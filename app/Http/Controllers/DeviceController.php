<?php

namespace App\Http\Controllers;

use App\Models\DeviceConnection;
use App\Models\GrowthMeasurement;
use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $devices = DeviceConnection::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('devices.index', compact('devices'));
    }

    /**
     * API: Return devices as JSON for AJAX refresh.
     */
    public function apiIndex()
    {
        $devices = DeviceConnection::where('user_id', Auth::id())
            ->with('user')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'devices' => $devices,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('devices.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_name' => 'required|string|max:255',
            'device_type' => 'required|in:weight_scale,height_rod,muac_tape,infantometer,multi_function',
            'serial_number' => 'required|string|max:255|unique:device_connections,serial_number',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'connection_type' => 'nullable|string|max:255',
            'com_port' => 'nullable|string|max:20',
            'baud_rate' => 'nullable|integer|min:1200|max:921600',
            'data_bits' => 'nullable|integer|min:5|max:8',
            'parity' => 'nullable|string|in:none,even,odd,mark,space',
            'stop_bits' => 'nullable|integer|min:1|max:2',
            'data_format' => 'nullable|string|max:255',
            'calibration_data' => 'nullable|json',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_active'] = true;

        $device = DeviceConnection::create($validated);

        return redirect()->route('devices.index')
            ->with('success', 'Device registered successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DeviceConnection $device)
    {
        // Check authorization
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        $recentMeasurements = GrowthMeasurement::where('device_id', $device->serial_number)
            ->latest()
            ->limit(10)
            ->get();

        return view('devices.show', compact('device', 'recentMeasurements'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DeviceConnection $device)
    {
        // Check authorization
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        return view('devices.edit', compact('device'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeviceConnection $device)
    {
        // Check authorization
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'device_name' => 'required|string|max:255',
            'device_type' => 'required|in:weight_scale,height_rod,muac_tape,infantometer,multi_function',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'com_port' => 'nullable|string|max:20',
            'baud_rate' => 'nullable|integer|min:1200|max:921600',
            'data_bits' => 'nullable|integer|min:5|max:8',
            'parity' => 'nullable|string|in:none,even,odd,mark,space',
            'stop_bits' => 'nullable|integer|min:1|max:2',
            'data_format' => 'nullable|string|max:255',
            'calibration_data' => 'nullable|json',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $device->update($validated);

        return redirect()->route('devices.show', $device)
            ->with('success', 'Device updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeviceConnection $device)
    {
        // Check authorization
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        $device->delete();

        return redirect()->route('devices.index')
            ->with('success', 'Device removed successfully.');
    }

    /**
     * Connect to device (simulate connection)
     */
    public function connect(DeviceConnection $device, Request $request)
    {
        // Check authorization
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        // Simulate device connection
        $device->markAsConnected();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Device connected successfully',
                'device' => $device,
            ]);
        }

        return back()->with('success', 'Device connected successfully.');
    }

    /**
     * Disconnect from device
     */
    public function disconnect(DeviceConnection $device, Request $request)
    {
        // Check authorization
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Device disconnected successfully',
            ]);
        }

        return back()->with('success', 'Device disconnected successfully.');
    }

    /**
     * Calibrate device
     */
    public function calibrate(DeviceConnection $device, Request $request)
    {
        // Check authorization
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'offset' => 'nullable|numeric',
            'factor' => 'nullable|numeric|min:0.1|max:10',
        ]);

        $calibrationData = $device->calibration_data ?? [];
        $calibrationData = array_merge($calibrationData, $validated);

        $device->update([
            'calibration_data' => $calibrationData,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Device calibrated successfully',
                'calibration' => $calibrationData,
            ]);
        }

        return back()->with('success', 'Device calibrated successfully.');
    }

    /**
     * API endpoint to receive data from connected devices
     */
    public function receiveData(Request $request)
    {
        $validated = $request->validate([
            'device_serial' => 'required|string|exists:device_connections,serial_number',
            'child_unique_id' => 'required|string|exists:children,unique_id',
            'measurement_type' => 'required|in:weight,height,head_circumference,muac',
            'value' => 'required|numeric',
            'unit' => 'required|string',
            'timestamp' => 'nullable|date',
        ]);

        $device = DeviceConnection::where('serial_number', $validated['device_serial'])->first();
        $child = Child::where('unique_id', $validated['child_unique_id'])->first();

        // Check authorization
        if ($child->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Apply calibration
        $calibratedValue = $device->applyCalibration($validated['value']);

        // Create measurement based on type
        $measurementData = [
            'child_id' => $child->id,
            'user_id' => Auth::id(),
            'measurement_date' => $validated['timestamp'] ?? now(),
            'is_from_device' => true,
            'device_id' => $device->serial_number,
        ];

        switch ($validated['measurement_type']) {
            case 'weight':
                $measurementData['weight'] = $calibratedValue;
                break;
            case 'height':
                $measurementData['height'] = $calibratedValue;
                break;
            case 'head_circumference':
                $measurementData['head_circumference'] = $calibratedValue;
                break;
            case 'muac':
                $measurementData['mid_upper_arm_circumference'] = $calibratedValue;
                break;
        }

        // Check if there's an existing measurement for today
        $existingMeasurement = GrowthMeasurement::where('child_id', $child->id)
            ->whereDate('measurement_date', $measurementData['measurement_date'])
            ->first();

        if ($existingMeasurement) {
            // Update existing measurement
            $existingMeasurement->update($measurementData);
            $measurement = $existingMeasurement->fresh();
        } else {
            $measurement = GrowthMeasurement::create($measurementData);
        }

        // Calculate Z-scores
        $whoService = new \App\Services\WHOGrowthService();
        $zScoreResult = $whoService->calculateZScores($measurement, $child);

        if ($zScoreResult['success']) {
            $measurement->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Measurement recorded successfully',
            'measurement' => $measurement,
            'z_scores' => $zScoreResult,
        ]);
    }
}