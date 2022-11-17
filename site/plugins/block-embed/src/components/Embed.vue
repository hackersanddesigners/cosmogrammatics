<template>
  <k-block-figure
    :is-empty="!source"
    empty-icon="file-code"
    empty-text="No embed selected yet …"
    @open="open"
    @update="update"
  >
    <div class="k-block-type-embed-wrapper">
      <div class="block-embed-container">
        <div v-html="source" class="block-embed-preview"></div>
        <div class="block-embed-preview-background"></div>
      </div>
      <h3>{{ content.title }}</h3>
      <p>{{ content.caption }}</p>
    </div>
  </k-block-figure>
</template>

<script>
export default {
   computed: {
     source() {
       if (this.content.source_url
           && this.content.source_url.media
           && this.content.source_url.media.code) {
         return this.content.source_url.media.code
       } else {
         return ''
       }
     }
   }
};
</script>

<style>
.block-embed-container {
  position: relative;
}

.block-embed-preview {
  position: relative;
  overflow: hidden;
  width: 100%;
  padding-top: 56.25%; /* 16:9 */
}

.block-embed-preview iframe {
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  width: 100%;
  height: 100%;
  max-width: 100%;
  z-index: 1;
}

.block-embed-preview-background {
    position: absolute;
    top: 0;
    left: 0;
    z-index: 0;
    width: 100%;
    height: 100%;
    background: #efefef url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxwYXR0ZXJuIGlkPSJhIiB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiPjxwYXRoIGZpbGw9InJnYmEoMCwgMCwgMCwgMC4yKSIgZD0iTTAgMGgxMHYxMEgwem0xMCAxMGgxMHYxMEgxMHoiLz48L3BhdHRlcm4+PHJlY3QgZmlsbD0idXJsKCNhKSIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIvPjwvc3ZnPg==);
    opacity: .45;
}
</style>