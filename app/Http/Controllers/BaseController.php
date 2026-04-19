<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * BaseController
 * Classe abstraite centralisant les réponses JSON et la gestion d'erreurs
 * pour tous les contrôleurs API du projet.
 */
abstract class BaseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Retourne une réponse JSON de succès
     */
    protected function respondSuccess(string $message, mixed $data = null, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data
        ], $status);
    }

    /**
     * Retourne une réponse JSON d'erreur
     */
    protected function respondError(string $message, array $errors = [], int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors
        ], $status);
    }

    /**
     * Exécute une action dans un bloc try-catch centralisé.
     * Capture automatiquement ValidationException, ModelNotFoundException et Exception.
     */
    protected function execute(callable $action): JsonResponse
    {
        try {
            return $action();
        } catch (ValidationException $e) {
            return $this->respondError('Erreur de validation des données.', $e->errors(), 422);
        } catch (ModelNotFoundException $e) {
            return $this->respondError('L\'élément demandé est introuvable ou a été archivé.', [], 404);
        } catch (Exception $e) {
            Log::error('Erreur Contrôleur: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return $this->respondError(
                'Une erreur interne est survenue.',
                config('app.debug') ? ['debug' => $e->getMessage()] : [],
                500
            );
        }
    }
}