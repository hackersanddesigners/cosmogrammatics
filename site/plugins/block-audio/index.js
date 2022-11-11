panel.plugin('cosmo/block-audio', {
  blocks: {
    audio: {
      computed: {
        source() {
          if (this.content.source_file[0]) {
            return this.content.source_file[0]

          } else if (this.content.source_embed[0]) {
            return this.content.source_embed[0]

          } else {
            {}
          }
        }
      },
      template: `
        <div>
          <div v-if="source.url">
            <h1>{{ content.title }}</h1>
            <audio controls style="width: 100%">
              <source :src="source.url" type="audio/mpeg">
            </audio>
          </div>
          <div v-else>No audio selected</div>
        </div>
      `
    }
  }
});

  // <div v-if="source_file.url">
  // <h3>{{ content.title }}</h3>
  // <audio controls>
  // <source :src="content.source_file.url" type="audio/mpeg">
  // </audio>
  // </div>
  // <div v-elseif="source_embed.url">
  // <p>{{ content.source_embed.url }}</p>
  // <div v-else>No audio added</div>
