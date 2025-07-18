<template>

  <!-- <div class="cbtt-form-form border border-brown-500 rounded-lg p-10">
    <form @submit.prevent="submitForm">

      <div class="form-group">
        <label>Tour Title : </label>
        <input v-model="form.title" type="text" readonly>
      </div>

      <div class="form-group">
        <label>Name*</label>
        <input v-model="form.name" type="text" required>
      </div>
      
      <div class="form-group">
        <label>Email*</label>
        <input v-model="form.email" type="email" required>
      </div>
      
      <div class="form-group">
        <label>Contact Num</label>
        <input v-model="form.phone" type="tel">
      </div>
      
      <div class="form-group">
        <label>Tour Date</label>
        <input v-model="form.tour_date" type="date">
      </div>
      
      <div class="form-group">
        <label>Message</label>
        <textarea v-model="form.message" rows="4"></textarea>
      </div>
      
      <div v-if="message" :class="['response', messageType]">
        {{ message }}
      </div>
      
      <button type="submit" :disabled="loading">
        {{ loading ? 'Sending...' : 'Submit Request' }}
      </button>


    </form>

    <button 
      :disabled="loading" 
      @click="submitForm2" 
      class="mt-4 border border-blue-300 bg-blue-300 p-5"
      >
      test
    </button>

  </div> -->


      <div class="border border-gray-400 rounded-lg p-3 w-100 pt-5">
        <form @submit.prevent="submitForm">

          <div class="relative w-full mb-4">
            <input
              type="text"
              id="tour-title"
              v-model="form.title"
              placeholder=" "
              required
              readonly
              class="peer h-12 w-full border border-gray-300 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-green-500 pl-3 text-xs"

            />
            <label
              for="tour-title"
              class="
                absolute left-[10px] -top-2 text-orange-500 text-xs transition-all
                peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
                peer-focus:-top-2 peer-focus:text-orange-500 peer-focus:text-xs bg-white pl-1 pr-1
              "
            >
              Tour Title
            </label>
          </div>


          <div class="relative w-full mb-4">
            <input
              type="text"
              id="name"
              v-model="form.name"
              placeholder=" "
              required
              class="peer h-12 w-full border border-gray-300 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-green-500 pl-3 text-sm"

            />
            <label
              for="name"
              class="
                absolute left-[10px] -top-2 text-orange-500 text-xs transition-all
                peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
                peer-focus:-top-2 peer-focus:text-orange-500 peer-focus:text-xs bg-white pl-1 pr-1
              "
            >
              Full Name
            </label>
          </div>

          <div class="relative w-full mb-4">
            <input
              type="text"
              id="contact"
              v-model="form.contact"
              placeholder=" "
              required
              class="peer h-12 w-full border border-gray-300 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-green-500 pl-3 text-sm"

            />
            <label
              for="contact"
              class="
                absolute left-[10px] -top-2 text-orange-500 text-xs transition-all
                peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
                peer-focus:-top-2 peer-focus:text-orange-500 peer-focus:text-xs bg-white pl-1 pr-1
              "
            >
              Contact Number
            </label>
          </div>

          <div class="relative w-full mb-4">
            <input
              type="email"
              id="email"
              v-model="form.email"
              placeholder=" "
              required
              class="peer h-12 w-full border border-gray-300 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-green-500 pl-3 text-sm"

            />
            <label
              for="email"
              class="
                absolute left-[10px] -top-2 text-orange-500 text-xs transition-all
                peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
                peer-focus:-top-2 peer-focus:text-orange-500 peer-focus:text-xs bg-white pl-1 pr-1
              "
            >
              Email Address
            </label>
          </div>

          <div class="relative w-full mb-4">
            <input
              type="date"
              id="tourdate"
              v-model="form.tour_date"
              placeholder=" "
              required
              class="peer h-12 w-full border border-gray-300 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-green-500 pl-3 text-sm"
            />

            <label
              for="tourdate"
              class="
                absolute left-[10px] -top-2 text-orange-500 text-xs transition-all
                peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
                peer-focus:-top-2 peer-focus:text-orange-500 peer-focus:text-xs bg-white pl-1 pr-1
              "
            >
              Tour Date
            </label>
          </div>

          <div class="relative w-full mb-4">
            <input
              type="text"
              id="pickup"
              v-model="form.pickup"
              placeholder=" "
              required
              class="peer h-12 w-full border border-gray-300 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-green-500 pl-3 text-sm"

            />
            <label
              for="pickup"
              class="
                absolute left-[10px] -top-2 text-orange-500 text-xs transition-all
                peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
                peer-focus:-top-2 peer-focus:text-orange-500 peer-focus:text-xs bg-white pl-1 pr-1
              "
            >
              Pick-up Location
            </label>
          </div>

          <div class="relative w-full mb-4">
            <input
              type="number"
              id="local"
              v-model="form.local"
              placeholder=" "
              required
              min="0"
              class="peer h-12 w-full border border-gray-300 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-green-500 pl-3 text-sm"

            />
            <label
              for="local"
              class="
                absolute left-[10px] -top-2 text-orange-500 text-xs transition-all
                peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
                peer-focus:-top-2 peer-focus:text-orange-500 peer-focus:text-xs bg-white pl-1 pr-1
              "
            >
              Number of Local Guest
            </label>
          </div>

          <div class="relative w-full mb-4">
            <input
              type="number"
              id="foreign"
              v-model="form.foreign"  
              placeholder=" "
              required
              min="0"
              class="peer h-12 w-full border border-gray-300 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-green-500 pl-3 text-sm"

            />
            <label
              for="foreign"
              class="
                absolute left-[10px] -top-2 text-orange-500 text-xs transition-all
                peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
                peer-focus:-top-2 peer-focus:text-orange-500 peer-focus:text-xs bg-white pl-1 pr-1
              "
            >
              Number of Foreign Guest
            </label>
          </div>

          <div class="flex items-center space-x-2 mb-4">
            <input
              id="camera"
              name="camera"
              v-model="form.camera"
              type="checkbox"
              class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
            />
            <label for="camera" class="text-sm text-gray-700">
              Camera Rental
            </label>
          </div>

          <!-- <div class="flex items-center space-x-2 mb-4">
            <input
              id="agree"
              name="agree"
              type="checkbox"
              class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
            />
            <label for="agree" class="text-sm text-gray-700">
              I agree to the terms & conditions
            </label>

          </div> -->

          <div class="relative w-full mb-4">
            <textarea
              id="message"
              placeholder=" "
              v-model="form.message"
              required
              rows="4"
              class="peer w-full border border-gray-300 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-green-500 p-3 resize-none text-sm"
            ></textarea>

            <label
              for="message"
              class="
                absolute left-[10px] -top-2 text-orange-500 text-xs transition-all
                peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
                peer-focus:-top-2 peer-focus:text-orange-500 peer-focus:text-xs bg-white pl-1 pr-1
              "
            >
              Additional Tour Notes/Requests
            </label>
          </div>

          <div class="text-right">
            <button
              type="submit"
              class="px-5 py-2 bg-[#0C91EB] text-white rounded-lg shadow hover:bg-[#0a7fcb] focus:outline-none focus:ring-2 focus:ring-[#0C91EB] focus:ring-offset-2 transition text-sm"
            >
              Submit
            </button>
          </div>

        <button 
          :disabled="loading" 
          @click="submitForm2" 
          class="mt-4 border border-blue-300 bg-blue-300 px-5 py-2"
          >
          test
        </button>


        </form>
      </div>



</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router'

const emit = defineEmits(['formSubmitted']);

const props = defineProps({
  postTitle: {
    type: String,
    required: true,
  },
});

console.log('Post title from props:', props.postTitle);

const router = useRouter()
const postTitle = ref('Default Title');

const form = ref({
  title: props.postTitle,
  name: '',
  contact: '',
  email: '',
  phone: '',
  tour_date: '',
  pickup: '',
  local: 0,
  foreign: 0,
  camera: false,
  message: ''
});


const loading = ref(false);
const message = ref('');
const messageType = ref('');

const submitForm1 = async () => {
  loading.value = true;
  message.value = '';
  
  try {
    const response = await window.jQuery.post(ajaxurl, {
      action: 'tour_form_submit',
      security: tourFormAjax.nonce,
      ...form.value
    });

    if (response.success) {
      message.value = response.data.message;
      messageType.value = 'success';
      resetForm();
    } else {
      message.value = response.data.message;
      messageType.value = 'error';
    }
  } catch (error) {
    message.value = 'An error occurred. Please try again.';
    messageType.value = 'error';
  } finally {
    loading.value = false;
  }
};

const submitForm = async () => {
    console.log("Form data: ", form.value);
    router.push({
        name: 'validate-form',
        query: { data: JSON.stringify(form.value) }
    });
    // emit ('formSubmitted', form.value);
}

const resetForm = () => {
  form.value = {
    name: '',
    email: '',
    phone: '',
    tour_date: '',
    message: ''
  };
};


onMounted(async () => {

  const siteUrl = window.cbttApp?.siteUrl || '';
  const restUrl = window.cbttApp?.restUrl || '';

  const nonce = window.wpApiSettings?.nonce || '';

  console.log('Site URL:', siteUrl);
  console.log('REST URL:', restUrl);

  // console.log('This is the mounted hook in contact form!!!');
  // try {
    const response = await fetch(restUrl + 'cbtt/v1/get-posts', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        // If you need authentication, add nonce or auth headers here
      },
      // body: JSON.stringify(form.value)
    });

    const data = await response.json();
    console.log('Response data:', data);

  //   if (data.success) {

  //     console.log('Data received:', data);

  //     // message.value = data.message;
  //     // messageType.value = 'success';
  //     // resetForm();
  //   } else {
  //     // message.value = data.message || 'Submission failed.';
  //     // messageType.value = 'error';
  //   }
  // } catch (error) {
  //   // message.value = 'An error occurred. Please try again.';
  //   // messageType.value = 'error';
  // } finally {
  //   // loading.value = false;
  // }  

  // Access the data attribute from the root element
  const el = document.getElementById('vue-contact-form');
  postTitle.value = el?.dataset.postTitle || 'Default Title';


});

const submitForm2 = async () => {

  const siteUrl = window.cbttApp?.siteUrl || '';
  const restUrl = window.cbttApp?.restUrl || '';
  const nonce = window.wpApiSettings?.nonce || '';

  console.log("Form data: ", form.value);
  const payload = ref({
    name: form.value.name,
    email: form.value.email,
    phone: form.value.phone,
    title: form.value.title,
    title: form.value.tour_date,
  })

  try {
    const response = await fetch(restUrl + 'cbtt/v1/submit-form', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': window.cbttApp.nonce
      },
      body: JSON.stringify(payload.value)
    });

    const data = await response.json();
    console.log('Response data:', data);
  } catch (error) {
    console.error('Error submitting form:', error);
  }


};

</script>
