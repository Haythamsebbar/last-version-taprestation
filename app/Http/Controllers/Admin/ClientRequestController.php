<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClientRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ClientRequest::with(['client.user'])
            ->orderBy('created_at', 'desc');

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhereHas('client.user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $clientRequests = $query->paginate(15);

        return view('admin.client-requests.index', compact('clientRequests'));
    }

    /**
     * Display the specified resource.
     */
    public function show(ClientRequest $clientRequest)
    {
        $clientRequest->load(['client.user', 'offers.prestataire.user']);
        
        return view('admin.client-requests.show', compact('clientRequest'));
    }

    /**
     * Update the status of the specified resource.
     */
    public function updateStatus(Request $request, ClientRequest $clientRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled'
        ]);

        $clientRequest->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Statut de la demande mis à jour avec succès.');
    }

    /**
     * Export client requests data.
     */
    public function export(Request $request)
    {
        $query = ClientRequest::with(['client.user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $clientRequests = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="client-requests-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($clientRequests) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Titre', 'Client', 'Email', 'Catégorie', 'Budget', 'Statut', 'Date de création']);

            foreach ($clientRequests as $request) {
                fputcsv($file, [
                    $request->id,
                    $request->title,
                    $request->client->user->name ?? 'N/A',
                    $request->client->user->email ?? 'N/A',
                    $request->category,
                    $request->budget,
                    $request->status,
                    $request->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClientRequest $clientRequest)
    {
        $clientRequest->delete();
        
        return redirect()->route('administrateur.client-requests.index')
                        ->with('success', 'Demande client supprimée avec succès.');
    }
}
