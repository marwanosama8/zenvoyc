<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('invoice.month2month') }} {{date('Y')}}
        </h2>
    </x-slot>
    <div class="w-full">
        <div class="flex mt-12 items-center justify-center">
            <div class="grid bg-white rounded-lg shadow-xl w-11/12 md:w-9/12 lg:w-2/3">
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Monat
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Betrag
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($data as $key => $value)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <p
                                                    class="text-sm text-gray-900 font-bold">{{DateTime::createFromFormat('!m', $key)->format('F')}}</p>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <p class="text-green-600 font-bold"> {{number_format($value,2,',','.')}} €</p>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
