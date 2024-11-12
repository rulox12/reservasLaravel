<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {ref, inject, computed} from 'vue';
import axios from 'axios';
import UserModal from './User/UserModal.vue';

const swal = inject('$swal');

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
});

const reactiveUsers = ref([...props.users]);
const searchType = ref('');
const searchValue = ref('');

const editUser = (user) => {
    selectedUser.value = user;
    isModalOpen.value = true;
};

const deleteUser = async (userId) => {
    const confirmed = await swal({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "Eliminar",
    });

    if (confirmed.isConfirmed) {
        try {
            await axios.delete(route('users.destroy', userId));

            swal({
                title: '¡Eliminado!',
                text: '¡Usuario eliminado con éxito!',
                icon: 'success',
                button: 'OK',
            });

            reactiveUsers.value = reactiveUsers.value.filter(user => user.id !== userId);
        } catch (error) {
            console.log(error);
            swal({
                title: '¡Error!',
                text: 'Hubo un problema al eliminar el usuario.',
                icon: 'error',
                button: 'OK',
            });
        }
    } else {
        wal({
            title: 'Cancelado',
            text: 'El usuario no fue eliminado.',
            icon: 'info',
            button: 'OK',
        });
    }
};

const createUser = () => {
    selectedUser.value = {};
    isModalOpen.value = true;
};

const isModalOpen = ref(false);
const selectedUser = ref({});

const closeModalHandler = () => {
    isModalOpen.value = false;
};

const saveUser = async (user) => {
    try {
        if (user.id) {
            await axios.put(route('users.update', user.id), user);

            swal({
                title: '¡Éxito!',
                text: '¡Usuario actualizado con éxito!',
                icon: 'success',
                button: 'OK',
            });

            const index = reactiveUsers.value.findIndex(u => u.id === user.id);
            if (index !== -1) {
                reactiveUsers.value[index] = user;
            }
        } else {
            const response = await axios.post(route('users.store'), user);

            if (response.data.success) {
                swal({
                    title: '¡Éxito!',
                    text: response.data.message,
                    icon: 'success',
                    button: 'OK',
                });

                reactiveUsers.value.push(response.data.user);
            }
        }
    } catch (error) {
        swal({
            title: '¡Error!',
            text: 'Hubo un problema al guardar el usuario.',
            icon: 'error',
            button: 'OK',
        });
    }
    isModalOpen.value = false;
};

const searchUser = async () => {
    if (searchType.value && searchValue.value) {
        try {
            const response = await axios.get(route('users.search'), {
                params: {
                    type: searchType.value,
                    value: searchValue.value,
                },
            });

            reactiveUsers.value = response.data.users;
        } catch (error) {
            console.error('Error buscando usuarios:', error);
            swal({
                title: 'Error',
                text: 'Hubo un problema al realizar la búsqueda.',
                icon: 'error',
                button: 'OK',
            });
        }
    } else {
        console.log(searchType.value , searchValue.value);
        reactiveUsers.value = [...props.users];
    }
};
</script>
<template>
    <Head title="Users Dashboard"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Usuario
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="flex justify-between mb-4">
                    <button @click="createUser" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">
                        Añadir Nuevo Usuario
                    </button>
                </div>
                <div class="mb-4">
                    <div class="flex gap-6">
                    <select
                        v-model="searchType"
                        class="border border-gray-300 px-6 py-3 rounded-lg text-lg w-full max-w-xs"
                        placeholder="Selecciona un tipo de búsqueda"
                    >
                        <option value="identification">Identificación</option>
                        <option value="full_name">Nombre Completo</option>
                    </select>
                    <input
                        v-model="searchValue"
                        type="text"
                        placeholder="Buscar usuario..."
                        class="border border-gray-300 px-6 py-3 rounded-lg text-lg w-full max-w-xs"
                    />
                    <button @click="searchUser" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">
                        Buscar
                    </button>
                    </div>

                </div>
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <div v-if="reactiveUsers?.length === 0" class="text-center">
                        <p class="text-gray-600">No hay usuarios disponibles. Por favor, agrega un usuario.</p>
                    </div>

                    <div v-else>
                        <table class="min-w-full">
                            <thead>
                            <tr>
                                <th class="px-4 py-2 text-left">Nombre Completo</th>
                                <th class="px-4 py-2 text-left">Correo Electrónico</th>
                                <th class="px-4 py-2 text-left">Rol</th>
                                <th class="px-4 py-2 text-left">Identificación</th>
                                <th class="px-4 py-2 text-left">Teléfono</th>
                                <th class="px-4 py-2 text-left">Ciudad</th>
                                <th class="px-4 py-2 text-left">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(user, index) in reactiveUsers" :key="user.id">
                                <td class="border px-4 py-2">{{ user.full_name }}</td>
                                <td class="border px-4 py-2">{{ user.email }}</td>
                                <td class="border px-4 py-2">{{ user.role }}</td>
                                <td class="border px-4 py-2">{{ user.identification }}</td>
                                <td class="border px-4 py-2">{{ user.phone }}</td>
                                <td class="border px-4 py-2">{{ user.city }}</td>
                                <td class="border px-4 py-2">
                                    <button @click="editUser(user)" class="text-green-500 hover:text-green-600">
                                        <svg class="h-5 w-5 text-green-300" viewBox="0 0 24 24" stroke-width="2"
                                             stroke="currentColor" fill="none" stroke-linecap="round"
                                             stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z"/>
                                            <path d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3"/>
                                            <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3"/>
                                            <line x1="16" y1="5" x2="19" y2="8"/>
                                        </svg>
                                    </button>
                                    <button @click="deleteUser(user.id)" class="text-red-500 hover:text-red-600 ml-2">
                                        <svg class="h-5 w-5 text-red-300" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="15" y1="9" x2="9" y2="15"/>
                                            <line x1="9" y1="9" x2="15" y2="15"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <UserModal
                    v-model:isOpen="isModalOpen"
                    v-model:user="selectedUser"
                    @close="closeModalHandler"
                    @save="saveUser"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
