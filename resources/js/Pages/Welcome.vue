<script setup>
import {Head, Link} from '@inertiajs/vue3';
import {inject, ref} from 'vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
    authUser: {

    }
});

const items = ref([]);
const startDate = ref('');
const endDate = ref('');
const capacity = ref('');
const airOption = ref('fan');
const minEndDate = ref('');
const showModal = ref(false);
const selectedRoom = ref(null);
const name = ref('');
const email = ref('');
const identification = ref('');
const phone = ref('');
const totalPrice = ref('');
const swal = inject('$swal');
const resultContainer = ref(null);

const updateMinEndDate = () => {
    if (startDate.value) {
        const startDateObj = new Date(startDate.value);
        startDateObj.setDate(startDateObj.getDate() + 1);
        minEndDate.value = startDateObj.toISOString().split('T')[0];
    } else {
        minEndDate.value = '';
    }
};

const handleSubmit = async () => {
    const requestData = {
        startDate: startDate.value,
        endDate: endDate.value,
        capacity: capacity.value,
        airConditioning: airOption.value,
    };

    const response = await axios.get(route('room.available.rooms'), {params: requestData});
    scrollToResult();

    if (response.data.success) {
        items.value = response.data.rooms;
    } else {
        alert('No se encontraron habitaciones para la fecha seleccionada');
    }
};

const images = ref([
    "https://i.ibb.co/pdCNLJk/2.jpg",
    "https://i.ibb.co/St2c6xD/1.jpg",
    "https://i.ibb.co/2ZpLS0v/4.jpg"
]);
const currentImageIndex = ref(0);

const prevImage = () => {
    currentImageIndex.value = (currentImageIndex.value - 1 + images.value.length) % images.value.length;
};

const nextImage = () => {
    currentImageIndex.value = (currentImageIndex.value + 1) % images.value.length;
};

const showReservationModal = (room) => {
    selectedRoom.value = room;
    totalPrice.value = calculateTotalPrice(room.price, startDate.value, endDate.value);
    showModal.value = true;
};

const calculateTotalPrice = (price, start, end) => {
    const startDate = new Date(start);
    const endDate = new Date(end);
    const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
    return days * price;
};

const confirmReservation = async () => {
    if (name.value && email.value) {
        showModal.value = false;
        const requestData = {
            startDate: startDate.value,
            endDate: endDate.value,
            totalPrice: totalPrice.value,
            email: email.value,
            name: name.value,
            phone: phone.value,
            identification: identification.value,
            room_id: selectedRoom.value.id
        }
        try {
            const response = await axios.post(route('booking.reservation'), requestData);
            swal({
                title: '¡Reserva Exitosa!',
                text: 'A su correo le llegará la confirmación de su reserva.',
                icon: 'success',
                button: 'OK',
            });
        } catch (e) {
            swal({
                title: 'Error!',
                text: e.response?.data?.message,
                icon: 'error',
                button: 'OK',
            });
        }
    } else {
        alert('Por favor, complete su nombre y correo electrónico.');
    }
};

const scrollToResult = () => {
    resultContainer?.value.scrollIntoView({behavior: 'smooth'});
};
</script>

<template>
    <Head title="Welcome"/>
    <header class="grid grid-cols-2 items-center gap-2 py-2 px-20 lg:grid-cols-3" style="background-color: #b4e3eb">
        <div class="flex lg:col-start-2 lg:justify-center">
            <img src="https://i.ibb.co/mFNKL1y/Whats-App-Image-2024-10-25-at-7-35-11-PM.jpg" alt="Logo"
                 class="h-20 w-auto lg:h-24"/>
        </div>
        <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
            <Link v-if="$page.props.auth.user" :href="route('profile.edit')"
                  class="rounded-md px-3 py-2 text-white ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                {{ authUser.name }}
            </Link>

            <template v-else>
                <!--<Link :href="route('login')"
                      class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                    Ingresar
                </Link>-->

                <!--<Link v-if="canRegister" :href="route('register')"
                      class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                    Registrar
                </Link>-->
            </template>
        </nav>
    </header>

    <section class="bg-cover bg-center py-20"
             style="background-image: url('https://static.vecteezy.com/system/resources/previews/025/871/495/non_2x/travel-destination-background-and-template-design-with-travel-destinations-and-famous-landmarks-and-attractions-for-tourism-let-s-go-travel-illustration-vector.jpg');">
        <div class="max-w-screen-lg mx-auto bg-white/80 p-10 rounded-lg shadow-lg">
            <h2 class="text-4xl font-extrabold text-center text-gray-800 mb-6"
                style="font-family: 'Poppins', sans-serif;">
                <span class="relative inline-block">

                    <span class="relative">Reserva tu Habitación</span>
                </span>
            </h2>
            <form @submit.prevent="handleSubmit" class="space-y-4">
                <div class="sm:flex sm:space-x-4">
                    <div class="flex-grow">
                        <label for="start-date" class="block mb-2 text-sm font-medium text-gray-900">Fecha desde</label>
                        <input type="date" id="start-date" v-model="startDate" :min="minStartDate"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                               required @change="updateMinEndDate">
                    </div>
                    <div class="flex-grow">
                        <label for="end-date" class="block mb-2 text-sm font-medium text-gray-900">Fecha hasta</label>
                        <input type="date" id="end-date" v-model="endDate" :min="minEndDate"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                               required>
                    </div>
                    <div class="flex-grow">
                        <label for="capacity" class="block mb-2 text-sm font-medium text-gray-900">Cantidad de
                            personas</label>
                        <select id="capacity" v-model="capacity"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                required>
                            <option value="">Selecciona capacidad</option>
                            <option value="2">2 personas</option>
                            <option value="4">4 personas</option>
                            <option value="6">6 personas</option>
                        </select>
                    </div>
                </div>
                <div class="sm:flex sm:space-x-4">
                    <div class="flex items-center">
                        <input id="air_conditioning" type="radio" v-model="airOption" value="air_conditioning"
                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <label for="air_conditioning" class="ml-2 text-sm font-medium text-gray-900">
                            Con aire acondicionado
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input id="fan" type="radio" v-model="airOption" value="fan"
                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <label for="fan" class="ml-2 text-sm font-medium text-gray-900">Con ventilador</label>
                    </div>
                </div>
                <button type="submit"
                        class="w-full sm:w-auto bg-[#394398] text-white px-4 py-2 rounded-lg text-center font-medium hover:bg-[#2c347a] focus:ring-4 focus:ring-purple-200">
                    Buscar habitación
                </button>
            </form>
        </div>
    </section>
    <section class="bg-white dark:bg-gray-900 py-12">
        <div class="max-w-screen-md mx-auto mb-8 text-center lg:mb-12">
            <h2 class="mb-4 text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">Descubre Todas
                Nuestras Habitaciones
            </h2>
            <p class="mb-5 font-light text-gray-500 sm:text-xl dark:text-gray-400">
                En Hoteles Pablo Almirante, ofrecemos una amplia
                variedad de habitaciones diseñadas para satisfacer las necesidades de tu equipo, cada una equipada con
                comodidades que garantizan una estancia agradable y productiva.
            </p>
        </div>
        <div class="max-w-screen-xl mx-auto grid gap-6 lg:grid-cols-3">
            <div v-for="(item, index) in items" :key="index"
                 class="bg-opacity-40 dark:bg-opacity-50 rounded-lg p-6 shadow-md" style="background: rgb(253,185,29)">
                <div class="flex justify-center">
                    <h3 class="text-xl justify-center font-bold text-white">{{ item.type }}</h3>
                </div>
                <h4 class="mt-2 text-white font-bold">Capacidad: {{ item.capacity }}</h4>
                <h4 class="mt-2 text-white font-bold">Habitación: {{ item.room_number }}</h4>
                <div class="flex items-baseline justify-center my-8">
                    <span class="mr-2 text-2xl font-extrabold text-white">{{ item.price }} /noche</span>
                </div>
                <div class="mb-4">
                    <ul class="list-disc list-inside text-white font-bold">
                        <li v-if="item.climate_control === 'air_conditioning'">Aire acondicionado</li>
                        <li>Acceso a piscina</li>
                        <li>Todas las comidas incluidas</li>
                    </ul>
                </div>
                <button @click="showReservationModal(item)"
                        class="w-full sm:w-auto bg-[#394398] text-white px-4 py-2 rounded-lg text-center font-medium hover:bg-[#2c347a] focus:ring-4 focus:ring-purple-200">
                    Reservar
                </button>
            </div>
        </div>
        <span ref="resultContainer"></span>
    </section>
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg max-w-md w-full h-[80vh] overflow-y-auto">
            <div class="mb-4">
                <div class="relative">
                    <img :src="images[currentImageIndex]" alt="Imagen de habitación" class="w-full rounded-lg"/>
                    <button @click="prevImage"
                            class="absolute top-1/2 left-2 transform -translate-y-1/2 bg-white p-1 rounded-full text-gray-700">
                        &lt;
                    </button>
                    <button @click="nextImage"
                            class="absolute top-1/2 right-2 transform -translate-y-1/2 bg-white p-1 rounded-full text-gray-700">
                        &gt;
                    </button>
                </div>
            </div>

            <h2 class="text-xl font-bold mb-4">Confirmación de Reserva</h2>
            <p class="mb-2"><strong>Habitación:</strong> {{ selectedRoom.type }}</p>
            <p class="mb-2"><strong>Capacidad:</strong> {{ selectedRoom.capacity }}</p>
            <p class="mb-2"><strong>Fecha desde:</strong> {{ startDate }}</p>
            <p class="mb-2"><strong>Fecha hasta:</strong> {{ endDate }}</p>
            <p class="mb-4"><strong>Total:</strong> {{ totalPrice }}</p>

            <!-- Campos de Nombre y Correo -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Identificación</label>
                <input type="number" id="identification" v-model="identification"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700" required>
            </div>
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nombre</label>
                <input type="text" id="name" v-model="name"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700" required>
            </div>
            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Celular</label>
                <input type="number" id="phone" v-model="phone"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Correo
                    Electrónico
                </label>
                <input type="email" id="email" v-model="email"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700" required>
            </div>
            <div class="flex justify-end space-x-4">
                <button @click="showModal = false"
                        class="w-full sm:w-auto bg-[#394398] text-white px-4 py-2 rounded-lg text-center font-medium hover:bg-[#2c347a] focus:ring-4 focus:ring-purple-200">
                    Cancelar
                </button>
                <button @click="confirmReservation" class="w-full sm:w-auto bg-[#394398] text-white px-4 py-2 rounded-lg text-center font-medium hover:bg-[#2c347a] focus:ring-4 focus:ring-purple-200">
                    Confirmar
                </button>
            </div>
        </div>
    </div>
</template>
