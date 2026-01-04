<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'results' => [],
                'total' => 0
            ]);
        }

        $results = [];

        // Buscar en socios
        $members = Member::where('firstname', 'ILIKE', "%{$query}%")
            ->orWhere('lastname', 'ILIKE', "%{$query}%")
            ->orWhere('member_id', 'ILIKE', "%{$query}%")
            ->limit(5)
            ->get();

        foreach ($members as $member) {
            $results[] = [
                'type' => 'member',
                'id' => $member->id,
                'title' => $member->firstname . ' ' . $member->lastname,
                'subtitle' => 'Socio - ID: ' . $member->member_id,
                'icon' => 'fa-user',
                'color' => 'primary',
                'url' => route('admin.socios.show', $member->id)
            ];
        }

        // Buscar en empleados/instructores
        $employees = Employee::where('firstname', 'ILIKE', "%{$query}%")
            ->orWhere('lastname', 'ILIKE', "%{$query}%")
            ->orWhere('employee_id', 'ILIKE', "%{$query}%")
            ->with('position')
            ->limit(5)
            ->get();

        foreach ($employees as $employee) {
            $positionName = $employee->position ? $employee->position->description : 'Sin Puesto';
            
            $results[] = [
                'type' => 'employee',
                'id' => $employee->id,
                'title' => $employee->firstname . ' ' . $employee->lastname,
                'subtitle' => 'Empleado - ' . $positionName,
                'icon' => 'fa-user-tie',
                'color' => 'success',
                'url' => route('admin.instructors.show', $employee->id)
            ];
        }

        return response()->json([
            'results' => $results,
            'total' => count($results),
            'query' => $query
        ]);
    }
}
