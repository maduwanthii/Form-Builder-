<script setup lang="ts">
import { shallowRef } from 'vue';

const footerLink = shallowRef([
  {
    title: 'About us'
  },
  {
    title: 'Privacy'
  },
  {
    title: 'Terms'
  }
]);
</script>
<template>
  <v-footer class="px-0 footer">
    <v-row justify="center" no-gutters>
      <v-col cols="6">
        <p class="text-caption mb-0">
          © 2025 All rights reserved
          
        </p>
      </v-col>
      <v-col class="text-right" cols="6">
        
      </v-col>
    </v-row>
  </v-footer>
</template>
