const Highlighter = require('web-highlighter')
const LocalStore = require('./local.store')


// <https://github.com/alienzhou/web-highlighter/blob/master/example/index.js>
function textHighlight(target, toolbar, article_slug) {

  const highlighter = new Highlighter({
    $root: document.querySelector('.content-wrapper'),
    wrapTag: 'span',
    exceptSelectors: ['.highlight-tip', 'aside']
  })

  highlighter.setOption({
    style: {
      className: 'bgc-accent',
    },
  })

  // -- restore all comments (drafts and published)
  //    which are mapped only to text-highlights with actual
  //    comments attached to it
  //    OR are block-level comments
  const comment_store = new LocalStore(`comment-${article_slug}`)
  comment_store.getAll().forEach(comment => {

    if (typeof comment === 'object') {
      if ('content' in comment && comment.content.selection_text !== undefined) {
        const hs = comment.content.selection_text
        highlighter.fromStore(hs.startMeta, hs.endMeta, hs.text, hs.id)
      }
    }
  });

  const highlight_store = new LocalStore()

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
      // console.log('CREATE')

      // close the comment box if it is toggled
      toolbar_hide(toolbar)

      // create span to allow removing the selection
      sources.forEach(source => {

        // -- setup text-selection
        const selectionNode = highlighter.getDoms(source.id)[0]
        const blockID = getBlockID(selectionNode)

        if (blockID == null) {
          // if blockID is missing or not set,
          // stop everything
          removeSelection(highlighter, source.id)
          return
        }

        const user_store = new LocalStore('user')
        const user = user_store.getByID(article_slug)

        // -- toggle comment input form
        const positionToolbar = getCoords(selectionNode)
        const block_type = 'text'
        toggle_toolbar(positionToolbar, toolbar, block_type, source.id, blockID, user)
        
        // -- save text-selection to highlight_store
        highlight_store.save(source)

        const form = toolbar.querySelector('form')
        form.querySelector('#selection_text').value = JSON.stringify(source)
      })

     })
    .on(Highlighter.event.REMOVE, ({ids}) => {
      // TODO implement remove action
      console.log('remove -', ids);
      // ids.forEach(id => store.remove(id));
    });

  // -- => start highlighter
  highlighter.run();

  function removeSelection(highlighter, id) {
    highlighter.remove(id);
  }

  function getBlockID(node) {
    const block = node.closest('section .block')

    if (block !== undefined && block !== null) {
      if (block.id) {
        return block.id
      }
    } else if (node.parentNode.dataset.bid !== undefined) {
      // this checks if instead of a block we're highlighting
      // a footnote with DOM <li id=note-ref-<ref> data-bid=<bid>>
      return node.parentNode.dataset.bid

    }

    return null
  }

  function toggle_toolbar(position, toolbar, blockType, sourceID, blockID, user) {
    const form = toolbar.querySelector('form')
    form.setAttribute('data-block-selection-type', blockType)
    form.setAttribute('data-block-comment-id', sourceID)
    form.setAttribute('data-block-id', blockID)

    form.querySelector('#selection_type').value = blockType
    form.querySelector('#block_id').value = blockID
    form.querySelector('#author').value = user.value

    // -- display input form for adding a comment
    toolbar.classList.remove('hidden')
    toolbar.style.setProperty( '--top', `${position.top}px` )
    toolbar.style.setProperty( '--left', `${position.left + position.width}px` )

    // handle click button to hide
    const btn_hide = toolbar.querySelector('.toolbar-btn-hide')
    btn_hide.addEventListener('click', () => {
      toolbar_hide(toolbar)

      const tip = document.querySelector(`[data-id="${sourceID}"]`)
      removeSelection(highlighter, sourceID)
    })

  }

  function toolbar_hide(toolbar) {
    toolbar.classList.add( 'hidden' )
    toolbar.style.removeProperty('--top')
    toolbar.style.removeProperty('--left')
  }

  function getCoords(elem) {
    let box = elem.getBoundingClientRect();

    return {
      top: box.top + window.pageYOffset,
      left: box.left + window.pageXOffset,
      width: box.width,
      height: box.height
    };
  }

}

module.exports = textHighlight;
