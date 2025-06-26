{{-- Vous devrez créer un layout principal pour l'agence : resources/views/agency/layout.blade.php --}}
@extends('agency.layout')

@section('title', 'Gestion des Taxis')

@section('content')
    <header class="p-4 sm:p-6 md:p-8 bg-white shadow-sm rounded-lg mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Gestion des Taxis</h1>
                <p class="text-sm text-gray-500 mt-1">Gérez votre flotte de véhicules.</p>
            </div>
            <a href="{{ route('agency.taxis.create') }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300 text-sm font-semibold">
                <i class="fas fa-plus mr-1"></i> Ajouter un Taxi
            </a>
        </div>
    </header>

    <div class="bg-white shadow-sm rounded-lg">
        <!-- Vue Mobile: Liste de cartes -->
        <div class="md:hidden">
            @forelse ($taxis as $taxi)
                <div class="p-4 border-b border-gray-200 last:border-b-0">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-bold text-gray-800">{{ $taxi->license_plate }}</p>
                            <p class="text-sm text-gray-600">{{ $taxi->model }} - {{ ucfirst($taxi->type) }}</p>
                            <p class="text-xs text-gray-500 mt-1"><i class="fas fa-city mr-1"></i> {{ $taxi->city->name }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full {{ $taxi->is_available ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $taxi->is_available ? 'Disponible' : 'En course' }}
                            </span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center mt-3">
                        <p class="text-sm text-gray-500">
                            <i class="fas fa-user-circle mr-1"></i>
                            {{ $taxi->driver->firstname ?? 'Non assigné' }}
                        </p>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('agency.taxis.edit', $taxi) }}" class="text-blue-500 hover:text-blue-700"><i
                                    class="fas fa-edit"></i></a>
                            <form action="{{ route('agency.taxis.destroy', $taxi) }}" method="POST"
                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce taxi ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700"><i
                                        class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="p-4 text-center text-gray-500">Aucun taxi trouvé.</p>
            @endforelse
        </div>

        <!-- Vue Desktop: Tableau -->
        <div class="hidden md:block">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Plaque</th>
                        <th scope="col" class="px-6 py-3">Modèle</th>
                        <th scope="col" class="px-6 py-3">Chauffeur Assigné</th>
                        <th scope="col" class="px-6 py-3">Ville</th>
                        <th scope="col" class="px-6 py-3">Statut</th>
                        <th scope="col" class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($taxis as $taxi)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ $taxi->license_plate }}</th>
                            <td class="px-6 py-4">{{ $taxi->model }}</td>
                            <td class="px-6 py-4">{{ $taxi->driver->firstname ?? 'Non assigné' }}</td>
                            <td class="px-6 py-4">{{ $taxi->city->name }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 font-semibold leading-tight rounded-full {{ $taxi->is_available ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $taxi->is_available ? 'Disponible' : 'En course' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right flex justify-end items-center space-x-3">
                                <a href="{{ route('agency.taxis.edit', $taxi) }}"
                                    class="font-medium text-blue-600 hover:underline">Modifier</a>
                                <form action="{{ route('agency.taxis.destroy', $taxi) }}" method="POST"
                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce taxi ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="font-medium text-red-600 hover:underline">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucun taxi trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4">
            {{ $taxis->links() }}
        </div>
    </div>
@endsection
