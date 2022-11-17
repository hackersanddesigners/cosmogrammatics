<template>
  <k-block-figure
    :is-empty="!source.url"
    empty-icon="file-video"
    empty-text="No video selected yet …"
    @open="open"
    @update="update"
  >
    <div class="k-block-type-video-wrapper">
      <video class="k-block-type-video-element" controls>
        <source :src="source.url" :type="mime">
      </video>
      <h3>{{ content.title }}</h3>
      <p>{{ content.caption }}</p>
    </div>
  </k-block-figure>
</template>

<script>
export default {
   data() {
     return {
       mime: null
     };
   },
   computed: {
     source() {
       if (this.content.source_file[0]) {
         return this.content.source_file[0];
       } else {
         return {};
       }
     }
   },
   watch: {
     "source.link": {
       handler (link) {
         if (link) {
           this.$api.get(link).then(file => {
             this.mime = file.mime;
           });
         }
       },
       immediate: true
     }
   }
};
</script>

<style>
.k-block-type-video-element {
  width: 100%;
}
</style>