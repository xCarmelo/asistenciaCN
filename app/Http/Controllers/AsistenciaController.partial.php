public function index(Request $request)
{
    $secciones = Seccion::where('estado', 1)->orderBy('nombre')->get();
    $cortes = Corte::all();

    $filtros = [
        'seccion_id' => $request->get('seccion_id'),
        'corte_id'   => $request->get('corte_id'),
        'desde'      => $request->get('desde', Carbon::now()->startOfMonth()->toDateString()),
        'hasta'      => $request->get('hasta', Carbon::now()->toDateString()),
    ];

    // Consulta base: asistencias con relaciones
    $query = Asistencia::with(['estudiante', 'seccion'])
        ->join('estudiantes', 'asistencias.id_estudiante', '=', 'estudiantes.id')
        ->whereBetween('asistencias.fecha', [$filtros['desde'], $filtros['hasta']])
        ->where('estudiantes.estado', 'Activo'); // solo estudiantes activos

    if ($filtros['corte_id']) {
        $query->where('asistencias.id_corte', $filtros['corte_id']);
    }
    if ($filtros['seccion_id']) {
        $query->where('asistencias.id_seccion', $filtros['seccion_id']);
    }

    // Agrupar por fecha, calcular presentes por género
    $asistenciasPorFecha = $query->selectRaw('
            asistencias.fecha,
            SUM(CASE WHEN estudiantes.genero = "F" AND asistencias.asis = "P" THEN 1 ELSE 0 END) as femeninas_presentes,
            SUM(CASE WHEN estudiantes.genero = "M" AND asistencias.asis = "P" THEN 1 ELSE 0 END) as varones_presentes
        ')
        ->groupBy('asistencias.fecha')
        ->orderBy('asistencias.fecha', 'desc')
        ->get();

    // Transformar para la vista
    $registros = $asistenciasPorFecha->map(function ($item) {
        return (object) [
            'fecha' => $item->fecha,
            'F' => $item->femeninas_presentes,
            'V' => $item->varones_presentes,
            'Total' => $item->femeninas_presentes + $item->varones_presentes,
        ];
    });

    return view('asistencia.index', compact('secciones', 'cortes', 'filtros', 'registros'));
}
