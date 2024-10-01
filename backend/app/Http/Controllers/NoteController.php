<?php

namespace App\Http\Controllers;

use App\Models\Note; // Asegúrate de tener un modelo llamado Note
use Illuminate\Http\Request;

class NoteController extends Controller
{
    // Función para crear una nueva nota
    public function create(Request $request) {
        // Validar la solicitud
        $validatedData = $request->validate([
            'data' => 'required|string|max:255',
        ]);

        // Crear una nueva nota
        $note = new Note();
        $note->data = $validatedData['data'];
        $note->save();

        // Retornar una respuesta exitosa
        return response()->json([
            'message' => 'Nota creada exitosamente',
            'data' => $note,
        ], 201);
    }

    // Función para obtener el listado de notas
    public function index() {
        // Obtener todas las notas ordenadas por fecha de creación
        $notes = Note::orderBy('created_at', 'desc')->get();

        // Retornar el listado de notas
        return response()->json($notes);
    }
}

