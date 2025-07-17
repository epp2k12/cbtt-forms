
<template>
    <ContactForm v-if="view=='form' && postTitle !== 'Default Title'" @form-submitted="handleFormSubmit" :post-title="postTitle"/>
    <ValidateForm v-else-if="view=='validate'" :form-data="formData" :post-title="postTitle"/>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import ContactForm from '@/components/ContactForm.vue';
import ValidateForm from '@/components/ValidateForm.vue';
const route = useRoute();
// console.log('Current path:', route.path);

const view = ref('form');
const formData = ref({});
const postTitle = ref('Default Title');

const handleFormSubmit = (data) => {
    // console.log('Form submitted with data:', data);
    formData.value = data;
    view.value = 'validate';
    // You can also pass the form data to the ValidateForm component if needed
};

onMounted(() => {
  // Access the data attribute from the root element
  const el = document.getElementById('vue-contact-form');
  postTitle.value = el?.dataset.postTitle || 'Default Title';

});

</script>

