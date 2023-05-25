panel.plugin("cosmo/block-iframe", {
  blocks: {
    iframe: {
      template: `
        <iframe
          v-if="content.source"
          :src="content.source"
        ></iframe>
        <p v-else>iFrame source not defned.</p>
      `
    }
  }
});
