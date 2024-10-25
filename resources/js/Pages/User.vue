<script setup>
// Importaciones
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, inject, watch } from 'vue';
import axios from 'axios';
import UserModal from './User/UserModal.vue';  // Cambiado a UserModal

const swal = inject('$swal');

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
});

const reactiveUsers = ref([...props.users]);

const editUser = (user) => {
    selectedUser.value = user;
    isModalOpen.value = true;
};

const deleteUser = async (userId) => {
    const confirmed = await swal({
        title: 'Are you sure?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "Delete",
    });

    if (confirmed.isConfirmed) {
        try {
            await axios.delete(route('users.destroy', userId));

            swal({
                title: 'Deleted!',
                text: 'User deleted successfully!',
                icon: 'success',
                button: 'OK',
            });

            // Actualizar la lista de usuarios reactivas
            reactiveUsers.value = reactiveUsers.value.filter(user => user.id !== userId);
        } catch (error) {
            console.log(error);
            swal({
                title: 'Error!',
                text: 'There was an issue deleting the user.',
                icon: 'error',
                button: 'OK',
            });
        }
    } else {
        swal({
            title: 'Cancelled',
            text: 'The user was not deleted.',
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
                title: 'Success!',
                text: 'User updated successfully!',
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
                    title: 'Success!',
                    text: response.data.message,
                    icon: 'success',
                    button: 'OK',
                });

                // Agregar el nuevo usuario a la lista
                reactiveUsers.value.push(response.data.user);
            }
        }
    } catch (error) {
        swal({
            title: 'Error!',
            text: 'There was an issue saving the user.',
            icon: 'error',
            button: 'OK',
        });
    }
    isModalOpen.value = false;
};
</script>
<template>
    <Head title="Users Dashboard"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Users
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="flex justify-between mb-4">
                    <button @click="createUser" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">
                        Add New User
                    </button>
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <div v-if="reactiveUsers?.length === 0" class="text-center">
                        <p class="text-gray-600">No users available. Please add a user.</p>
                    </div>

                    <div v-else>
                        <table class="min-w-full">
                            <thead>
                            <tr>
                                <th class="px-4 py-2 text-left">Name</th>
                                <th class="px-4 py-2 text-left">Email</th>
                                <th class="px-4 py-2 text-left">Role</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(user, index) in reactiveUsers" :key="user.id">
                                <td class="border px-4 py-2">{{ user.name }}</td>
                                <td class="border px-4 py-2">{{ user.email }}</td>
                                <td class="border px-4 py-2">{{ user.role }}</td>
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
