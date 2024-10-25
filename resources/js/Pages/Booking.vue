<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {ref, inject, watch} from 'vue';
import axios from 'axios';
import BookingModal from './Booking/BookingModal.vue';

const swal = inject('$swal');

const props = defineProps({
    bookings: {
        type: Array,
        required: true,
    },
});

const reactiveBookings = ref([...props.bookings]);
const users = ref([]);
const rooms = ref([]);

const editBooking = (booking) => {
    selectedBooking.value = booking;
    isModalOpen.value = true;
};

const deleteBooking = async (bookingId) => {
    const confirmed = await swal({
        title: 'Are you sure?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "Delete",
    });

    if (confirmed.isConfirmed) {
        try {
            await axios.delete(route('booking.destroy', bookingId));

            swal({
                title: 'Deleted!',
                text: 'Booking deleted successfully!',
                icon: 'success',
                button: 'OK',
            });

            reactiveBookings.value = reactiveBookings.value.filter(booking => booking.id !== bookingId);
        } catch (error) {
            console.log(error);
            swal({
                title: 'Error!',
                text: 'There was an issue deleting the booking.',
                icon: 'error',
                button: 'OK',
            });
        }
    } else {
        swal({
            title: 'Cancelled',
            text: 'The booking was not deleted.',
            icon: 'info',
            button: 'OK',
        });
    }
};

const createBooking = () => {
    selectedBooking.value = {};
    isModalOpen.value = true;
    fetchUsersAndRooms();
};

const fetchUsersAndRooms = async () => {
    try {
        const usersResponse = await axios.get(route('users.index'));
        const roomsResponse = await axios.get(route('rooms.index'));
        console.log(roomsResponse);
        users.value = usersResponse?.data?.users;
        rooms.value = roomsResponse?.data?.rooms;
    } catch (error) {
        console.error('Error fetching users or rooms:', error);
        swal({
            title: 'Error!',
            text: 'Could not fetch users or rooms.',
            icon: 'error',
            button: 'OK',
        });
    }
};

const isModalOpen = ref(false);
const selectedBooking = ref({});

const closeModalHandler = () => {
    isModalOpen.value = false;
};

const saveBooking = async (booking) => {
    try {
        if (booking.id) {
            await axios.put(route('booking.update', booking.id), booking);

            swal({
                title: 'Success!',
                text: 'Booking updated successfully!',
                icon: 'success',
                button: 'OK',
            });

            const index = reactiveBookings.value.findIndex(b => b.id === booking.id);
            if (index !== -1) {
                reactiveBookings.value[index] = booking;
            }
        } else {
            const response = await axios.post(route('booking.store'), booking);

            if (response.data.success) {
                swal({
                    title: 'Success!',
                    text: response.data.message,
                    icon: 'success',
                    button: 'OK',
                });

                reactiveBookings.value.push(response.data.booking);
            }
        }
    } catch (error) {
        swal({
            title: 'Error!',
            text: 'There was an issue saving the booking.',
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
                Bookings
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="flex justify-between mb-4">
                    <button @click="createBooking"
                            class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">
                        Add New Booking
                    </button>
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <div v-if="reactiveBookings?.length === 0" class="text-center">
                        <p class="text-gray-600">No bookings available. Please add a booking.</p>
                    </div>

                    <div v-else>
                        <table class="min-w-full">
                            <thead>
                            <tr>
                                <th class="px-4 py-2 text-left">Customer Name</th>
                                <th class="px-4 py-2 text-left">Room Number</th>
                                <th class="px-4 py-2 text-left">Check-in Date</th>
                                <th class="px-4 py-2 text-left">Check-out Date</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(booking, index) in reactiveBookings" :key="booking.id">
                                <td class="border px-4 py-2">{{ booking.user.name }}</td>
                                <td class="border px-4 py-2">{{ booking.room.room_number }}</td>
                                <td class="border px-4 py-2">{{ booking.check_in_date }}</td>
                                <td class="border px-4 py-2">{{ booking.check_out_date }}</td>
                                <td class="border px-4 py-2">{{ booking.status }}</td>
                                <td class="border px-4 py-2">
                                    <button @click="editBooking(booking)" class="text-green-500 hover:text-green-600">
                                        <svg class="h-5 w-5 text-green-300" viewBox="0 0 24 24" stroke-width="2"
                                             stroke="currentColor" fill="none" stroke-linecap="round"
                                             stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z"/>
                                            <path d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3"/>
                                            <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3"/>
                                            <line x1="16" y1="5" x2="19" y2="8"/>
                                        </svg>
                                    </button>
                                    <button @click="deleteBooking(booking.id)"
                                            class="text-red-500 hover:text-red-600 ml-2">
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

                <BookingModal
                    v-model:isOpen="isModalOpen"
                    v-model:booking="selectedBooking"
                    :users="users"
                    :rooms="rooms"
                    @close="closeModalHandler"
                    @save="saveBooking"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
