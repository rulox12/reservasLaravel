<script setup>
// Importaciones
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, inject, watch } from 'vue';
import axios from 'axios';
import RoomModal from './Room/RoomModal.vue';

const swal = inject('$swal');

const props = defineProps({
    rooms: {
        type: Array,
        required: true,
    },
});

const reactiveRooms = ref([...props.rooms]);

const editRoom = (room) => {
    selectedRoom.value = room;
    isModalOpen.value = true;
};

const deleteRoom = async (roomId) => {
    const confirmed = await swal({
        title: 'Are you sure?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "Delete",
    });

    if (confirmed.isConfirmed) {
        try {
            await axios.delete(route('room.destroy', roomId));

            swal({
                title: 'Deleted!',
                text: 'Room deleted successfully!',
                icon: 'success',
                button: 'OK',
            });

            // Actualizar la lista de habitaciones reactivas
            reactiveRooms.value = reactiveRooms.value.filter(room => room.id !== roomId);
        } catch (error) {
            console.log(error);
            swal({
                title: 'Error!',
                text: 'There was an issue deleting the room.',
                icon: 'error',
                button: 'OK',
            });
        }
    } else {
        swal({
            title: 'Cancelled',
            text: 'The room was not deleted.',
            icon: 'info',
            button: 'OK',
        });
    }
};

const createRoom = () => {
    selectedRoom.value = {};
    isModalOpen.value = true;
};

const isModalOpen = ref(false);
const selectedRoom = ref({});

const closeModalHandler = () => {
    isModalOpen.value = false;
};

const saveRoom = async (room) => {
    try {
        if (room.id) {
            await axios.put(route('room.update', room.id), room);

            swal({
                title: 'Success!',
                text: 'Room updated successfully!',
                icon: 'success',
                button: 'OK',
            });

            // Actualizar la lista con la habitación editada
            const index = reactiveRooms.value.findIndex(r => r.id === room.id);
            if (index !== -1) {
                reactiveRooms.value[index] = room;
            }
        } else {
            const response = await axios.post(route('room.store'), room);

            if (response.data.success) {
                swal({
                    title: 'Success!',
                    text: response.data.message,
                    icon: 'success',
                    button: 'OK',
                });

                // Agregar la nueva habitación a la lista
                reactiveRooms.value.push(response.data.room);
            }
        }
    } catch (error) {
        swal({
            title: 'Error!',
            text: 'There was an issue saving the room.',
            icon: 'error',
            button: 'OK',
        });
    }
    isModalOpen.value = false;
};
</script>

<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Rooms
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="flex justify-between mb-4">
                    <button @click="createRoom" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">
                        Add New Room
                    </button>
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <div v-if="reactiveRooms?.length === 0" class="text-center">
                        <p class="text-gray-600">No rooms available. Please add a room.</p>
                    </div>

                    <div v-else>
                        <table class="min-w-full">
                            <thead>
                            <tr>
                                <th class="px-4 py-2 text-left">Room Number</th>
                                <th class="px-4 py-2 text-left">Type</th>
                                <th class="px-4 py-2 text-left">Price</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(room, index) in reactiveRooms" :key="room.id">
                                <td class="border px-4 py-2">{{ room.room_number }}</td>
                                <td class="border px-4 py-2">{{ room.type }}</td>
                                <td class="border px-4 py-2">{{ room.price }}</td>
                                <td class="border px-4 py-2">{{ room.status }}</td>
                                <td class="border px-4 py-2">
                                    <button @click="editRoom(room)" class="text-green-500 hover:text-green-600">
                                        <svg class="h-5 w-5 text-green-300" viewBox="0 0 24 24" stroke-width="2"
                                             stroke="currentColor" fill="none" stroke-linecap="round"
                                             stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z"/>
                                            <path d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3"/>
                                            <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3"/>
                                            <line x1="16" y1="5" x2="19" y2="8"/>
                                        </svg>
                                    </button>
                                    <button @click="deleteRoom(room.id)" class="text-red-500 hover:text-red-600 ml-2">
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

                <RoomModal
                    v-model:isOpen="isModalOpen"
                    v-model:room="selectedRoom"
                    @close="closeModalHandler"
                    @save="saveRoom"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
