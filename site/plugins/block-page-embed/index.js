panel.plugin('cosmo/block-page-embed', {
  blocks: {
    page_embed: {
      computed: {
        page() {
          if (this.content.pageurl) {
            return this.content.pageurl
          } else {
            return {}
          }
        }
      },
      template: `
        <div>
          <div v-if="page.value">
            {{ content.pageurl.value[0]['text'] }}
          </div>
           <div v-else>No Page Embed yet.</div>
        </div>
      `
    }
  }
});
