<template> 
    <div class="min-h-screen bg-orange-100 flex items-center justify-center p-4 sm:p-6 lg:p-8 font-inter">
      <div class="bg-white rounded-lg shadow-xl p-6 sm:p-8 lg:p-10 max-w-lg w-full text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-blue-500 mb-4">
          Thank You!
        </h2>
        <p class="text-lg sm:text-xl text-gray-700 leading-relaxed">
          Dear <span class="font-semibold text-blue-500">{{ name }}</span>,
        </p>
        <p class="text-md sm:text-lg text-gray-700 leading-relaxed mt-2">
          Thank you for booking your <span class="font-semibold text-blue-500">{{ tour }}</span> tour with us!
          We appreciate your trust and look forward to providing you with an unforgettable experience.
        </p>
        <p class="text-md sm:text-lg text-gray-600 mt-6">
          A confirmation email with the details of your booking has been sent to your provided email address.
        </p>
        <div class="mt-8">
          <button
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-full shadow-lg transition duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50"
            @click="continueBrowsing"
          >
            Continue Browsing
          </button>
        </div>
      </div>
    </div>

</template>
<script setup> 
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const formData = ref(null)
const name = ref('')
const tour = ref('')
const formType = ref('advance')

onMounted(() => {

  formData.value = route.params.formData ? JSON.parse(route.params.formData) : null

  if (route.query.data) {
    formData.value = safelyParseJSON(route.query.data, {})
    name.value = formData.value.name || 'Guest'
    tour.value = formData.value.title || 'Tour'
  }

  formType.value = route.query.form_type

  console.log('Form Type:', formType.value);

});

const continueBrowsing = () => {

  switch (formType.value) {
    case 'advance':
      router.push({ name: 'contact' })
      break;
    case 'fast':
      router.push({ name: 'fast-contact' })
      break;
    case 'simple':
      router.push({ name: 'simple-contact-form' })
      break;
    default:
      router.push({ name: 'contact' })
  }

};

const safelyParseJSON = (jsonString, fallback) => {
  try {
    return JSON.parse(jsonString);
  } catch (error) {
    console.error('Failed to parse JSON:', error);
    return fallback;
  }
}

</script>