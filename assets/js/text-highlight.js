const Highlighter = require('web-highlighter')
const LocalStore = require('./local.store')
const {
  enable_commenting_state,
  make_comment_thread_el,
  make_comment_form_el,
} = require('./comments.js')

// <https://github.com/alienzhou/web-highlighter/blob/master/example/index.js>
function textHighlight( target, toolbar ) {
  const highlighter = new Highlighter({
    wrapTag: 'span',
    exceptSelectors: ['.highlight-tip']
  })

  highlighter.setOption({
    style: {
      className: 'bgc-accent',
    },
  })

  const store = new LocalStore();

  // restore all highlights to Highlighter instance
  store.getAll().forEach(hs => {
    highlighter.fromStore(hs.startMeta, hs.endMeta, hs.text, hs.id)
  });

  // except for the CREATE event
  // any other event is triggered when interacting
  // with an existing selection span
  highlighter
    .on(Highlighter.event.CLICK, ({id}) => {
      console.log('click -', id);
    })
    .on(Highlighter.event.HOVER, ({id}) => {
      console.log('hover -', id);
      highlighter.addClass('highlight-wrap-hover', id);
    })
    .on(Highlighter.event.HOVER_OUT, ({id}) => {
      console.log('hover out -', id);
      highlighter.removeClass('highlight-wrap-hover', id);
    })
    .on(Highlighter.event.CREATE, ({sources}) => {

      // // create span to allow removing the selection
      // sources.forEach(s => {
      //   const position = getPosition(highlighter.getDoms(s.id)[0]);
      //   createTag(position.top, position.left, s.id);
      // });

      // what to do if sources is > 1 and we need to position the comment toolbar?
      // pick last source in the list?

      if ( sources.length ) {
        enable_commenting_state()
        let source = sources[0]
        const selectionNode = highlighter.getDoms(source.id)[0]
        const block         = selectionNode.closest( 'section .block' )
        const block_id      = get_block_id( block )
        const position      = get_position( selectionNode )
        const aside         = block.querySelector( 'aside' )
        const template_form = aside.querySelector( '.thread form' )
        const form          = make_comment_form_el( template_form, source.id, block_id )
        const thread        = make_comment_thread_el( form, position )
        aside.appendChild( thread )
        form.firstElementChild.focus()
        store.save(sources)
      }

    })
    .on(Highlighter.event.REMOVE, ({ids}) => {
      // TODO implement remove action
      console.log('remove -', ids);
      // ids.forEach(id => store.remove(id));
    });

  highlighter.run();
}

function get_block_id( block ) {
  if (block !== undefined && block !== null) {
    if (block.id) {
      return block.id
    }
  }
  return ''
}

function get_position(node) {
  let offset = { top: 0, left: 0 }
  offset.top += node.getBoundingClientRect().top + document.documentElement.scrollTop
  offset.left += node.getBoundingClientRect().left + document.documentElement.scrollLeft
  return offset
}

// const createTag = (top, left, id) => {
//   const $span = document.createElement('span');
//   $span.style.left = `${left - 20}px`;
//   $span.style.top = `${top - 25}px`;
//   $span.style.position = 'absolute';
//   $span.dataset['id'] = id;
//   $span.textContent = 'delete';
//   $span.classList.add('highlight-tip');
//   document.body.appendChild($span);
//   console.log('create-tag =>', $span)
// };

// function toggle_toolbar(position, toolbar, sourceID, blockID) {
//   const form = toolbar.querySelector('form')
//   form.setAttribute('data-block-selection-type', 'text')
//   form.setAttribute('data-block-selection-text-id', sourceID)
//   form.setAttribute('data-block-id', blockID)

//   toolbar.classList.remove( 'hidden' )

//   toolbar.style.setProperty( '--top', position.top + 'px' )
//   toolbar.style.setProperty( '--left', position.left + 'px' )
// }

// <https://github.com/alienzhou/web-highlighter/blob/master/example/index.js#L55-L67>
// function getPosition($node) {
//   let offset = {
//     top: 0,
//     left: 0
//   };
//   while ($node) {
//     offset.top += $node.offsetTop;
//     offset.left += $node.offsetLeft;
//     $node = $node.offsetParent;
//   }
//   return offset;
// }

// function update_form(blockInfo) {
//   let form = document.querySelector('.toolbar form')
//   form.dataset.blockId = blockInfo.id
//   form.dataset.blockSelectionType = blockInfo.type
// }

module.exports = textHighlight;
