<template>
  <k-block-figure
    :is-empty="!page.value"
    empty-icon="audio-file"
    empty-text="No page selected yet …"
    @open="open"
    @update="update"
  >
    <div>
      <div v-if="page.value">
        {{ content.pageurl.value[0]['text'] }}
      </div>
    </div>
  </k-block-figure>
</template>

<script>
export default {
  computed: {
    page() {
      if (this.content.pageurl) {
        return this.content.pageurl
      } else {
        return {}
      }
    }
  }
};

</script>