<template>
  <div>
    <div v-if="source.url">
      <h1>{{ content.title }}</h1>
      <audio controls style="width: 100%">
        <source :src="source.url" type="audio/mpeg">
      </audio>
    </div>
    <div v-else>No audio selected</div>
  </div>
</template>

<script>
export default {
   computed: {
     source() {
       if (this.content.source_file[0]) {
         return this.content.source_file[0]
       } else {
         {}
       }
     }
   }
};
</script>