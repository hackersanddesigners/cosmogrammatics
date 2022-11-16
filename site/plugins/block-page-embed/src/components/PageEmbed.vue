<template>
  <k-block-figure
    :is-empty="!page.value"
    empty-icon="audio-file"
    empty-text="No page selected yet …"
    @open="open"
    @update="update"
  >
    <div>
      <header class="k-field-header">
        <label class="k-field-label">Page Embed</label>
      </header>
      <div v-if="page.value" class="k-blocks k-block-container k-block-container-type-page-embed">
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

<style>
.k-field-label {
  font-size: .8em;
  padding-bottom: 0;
  color: var(--color-text-light);
}

.k-block-container {
  position: relative;
  padding: .75rem;
  background: var(--color-white);
  border-radius: var(--rounded)
}

.k-block-container-type-page-embed {
  border-color: var(--color-purple);                                 
  border-width: 1px;
  border-style: solid;
}

</style>