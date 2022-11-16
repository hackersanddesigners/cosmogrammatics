<template>
  <div>
    <div v-if="source">
      <audio controls style="width: 100%">
        <source :src="source.url" type="audio/mpeg">
      </audio>
      <h3>{{ content.title }}</h3>
      <p>{{ content.caption }}</p>
    </div>
    <div v-else>No audio selected</div>
  </div>
</template>

<script>
export default {
   computed: {
     source() {
       if (this.content.source_file[0]) {
         return this.content.source_file[0];
       } else {
         {};
       }
     }
   }
};
</script>