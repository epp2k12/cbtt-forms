<template>
  <div>
    <!-- <h3>Debug Info:</h3>
    <p>Current Route: {{ $route.path }}</p>
    <p>Route Name: {{ $route.name }}</p>
    <p>Post Title: {{ postTitle }}</p>
    <hr> -->
    <router-view :post-title="postTitle" @form-submitted="handleFormSubmitted" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const postTitle = ref('Default Title');

const handleFormSubmitted = (formData) => {
  console.log('Form submitted with data:', formData);
};

onMounted(() => {
  const el = document.getElementById('vue-contact-form');
  postTitle.value = el?.dataset.postTitle || 'Default Title';
});
</script>
