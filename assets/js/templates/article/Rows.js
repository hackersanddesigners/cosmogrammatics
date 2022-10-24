// import { ref, computed } from '../../utils/vue.esm-browser.js'

export default {

  props: {
    rows: Array
  },

  setup( props ) {

    console.log( props.rows )

    return {}

  },

  template: `
    <section
      v-for="( row, i ) of rows"
      :key="i"
      class="row"
      data-bg-image=""
      data-bg-color=""
    >
      <section
        v-for="( col, j ) of row"
        :key="j"
        class="column"
        data-col-span="<?= col.span ?>"
        data-col-width="<?= col.width ?>"
      >
        <section
          v-for="( block, k ) of col.blocks"
          :key="k"
          tabindex="0"
          :class="[ 'block', block.type ]"
          :id="'b_' + block.id"
          :data-type="'block-' + block.type"
          v-html="block.text.value"
        >
        </section>
      </section>
    </section>`
}
