const LocalStore = require('./local.store')
const xss = require('xss')
const { commentReviewList, setUsername, updateCommentCounter } = require('./comment-review')

function respond_comment(e) {
  e.preventDefault()

  const form = e.target

  
  // -- save comment to localStorage under:
  //    <current-article-url>: [{..}, ...]

  // to correctly save object into LocalStore
  // the object needs to have an ID field
  // else LocalStore will replace the previous
  // comment with the newest one only
  // nb: check if selection-type => text

  const article_slug = window.location.pathname.split('/').pop().split('/').join('+')
  const comment_store = new LocalStore(`comment-${article_slug}`)
  let comment = make_comment(form, comment_store)
  if (!comment) {
    return
  }

  if (comment.content.selection_text !== undefined) {
    comment['id'] = comment.content.selection_text.id
  } else {
    comment['id'] = comment.content.block_id
  }

  // -- save username to local.store if not set yet
  setUsername(comment.content.user, comment.content.article_slug)
  
  comment_store.save(comment)
 
  // -- set comment-review list
  commentReviewList(article_slug)

  // -- create and append newly posted comment
  const article_comment = make_comment_el(comment)
  createComment(form, article_comment, comment)
  article_comment.focus()

}

function make_comment( form, store ) {
  // -- comment can either be text-selection
  //    or block-level comment
  //    how to avoid breaking the commenting system
  //    if there's no `selection_text`

  // -- check if text-comment is not empty,
  //    eg just empty spaces
  const children = Array.from(form.children)
  const input_body = children.find( c => c.name == 'body' )

  const input_text = xss(input_body.value.trim(), {
    whiteList:          {},        // empty, means filter out all tags
    stripIgnoreTag:     true,      // filter out all HTML not in the whilelist
    stripIgnoreTagBody: ['script'] // the script tag is a special case, we need to filter out its content
  })

  if (input_text === '') {
    // stop operation
    input_body.value = input_body.value.trim()
    input_body.focus()
    input_body.placeholder = 'Please add some text'

    input_body.style.border = '1px solid red'

    setTimeout(() => {
      input_body.style.border = ''
      input_body.placeholder = 'comment...'
    }, '3000')

    return false
  }

  const article_slug      = form.getAttribute( 'data-article-slug' )
  const block_id          = form.getAttribute( 'data-block-id' )
  const selection_type    = form.getAttribute( 'data-block-selection-type' )
  const comment_id        = form.getAttribute( 'data-block-comment-id' )
  const selection_text    = store.getByID(comment_id)
  const author            = xss(children.find( c => c.name == 'author' ).value, {
    whiteList:          {},        // empty, means filter out all tags
    stripIgnoreTag:     true,      // filter out all HTML not in the whilelist
    stripIgnoreTagBody: ['script'] // the script tag is a special case, we need to filter out its content
  })
  const text              = input_text
  const ts                = new Date().toISOString()

  return {
    slug: ts,
    title: ts,
    template: 'comment',
    status: 'draft',
    content: {
      user: author,
      timestamp: ts,
      article_slug: article_slug,
      block_id: block_id,
      text: text,
      selection_type: selection_type,
      selection_text: selection_text,
      // selection_coords: selection_coords
    }
  }
}

function make_comment_thread_el( form ) {
  const thread = document.createElement( 'section' )
  thread.classList.add( 'thread' )
  const thread_form = form.cloneNode( true )
  thread.appendChild( thread_form )
  return thread
}

function make_comment_el(data) {
  // -- section
  const text_comment = document.createElement('section')
  const section_text = data.content.text
  text_comment.append(section_text)

  // -- footer
  const footer = document.createElement('footer')
  footer.classList.add( 'small_italic' )
  const date = document.createElement('p')
  const timestamp = document.createElement('time')

  const ts = new Date(data.content.timestamp).toLocaleDateString('nl-NL', {
    timeZone: 'Europe/Berlin',
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: 'numeric',
    minute: 'numeric',
    second: 'numeric'
  })
  timestamp.setAttribute('datetime', data.content.timestamp)
  date.append(timestamp)
  date.innerHTML = `On ${ts}`

  const user = document.createElement('p')
  user.innerHTML = `by ${data.content.user}`

  footer.append(date)
  footer.append(user)

  // -- append everything to <article>
  const article = document.createElement('article')
  article.setAttribute('tabindex', '0')
  article.setAttribute('data-text-selection-id', data.id)
  
  article.classList.add('comment-draft')

  article.append(text_comment)
  article.append(footer)

  return article

}

function createComment(form, article, comment) {
  // -- append new comment to comment thread
  //    before <form>
  let thread

  // form: reset and hide form
  form.reset()
  form.blur()
  
  // check if the form is the toolbar form
  const form_parent = form.parentNode
  if (form_parent.classList.contains('toolbar')) {

    // make a new comment thread for this block
    thread = make_comment_thread_el(form)
    const block_id = form.getAttribute('data-block-id')

    if (block_id !== '') {
      const block = document.querySelector(`.content-wrapper section#${block_id}`)

      if (block !== null) {
        const aside = block.querySelector('aside')
        const thread_form = Array.from(thread.children)[0]
        
        thread.insertBefore(article, thread_form)
        aside.appendChild(thread)

        // hide input-form
        form_parent.classList.add('hidden')
        form_parent.style.removeProperty('--top')
        form_parent.style.removeProperty('--left')
      }
    }

  } else {
    thread = form_parent
    thread.insertBefore(article, form)
  }

  // if comment is at block-level, highlight block
  if (comment.content.selection_text === undefined && comment.id !== '') {
    const block = document.querySelector(`.content-wrapper #${comment.id}`)
    if (block !== null) {
      block.classList.add('block-highlight')
    }
  }

}

function commentsArticle(comment, form) {
  const article_comment = make_comment_el(comment)

  // set missing data to be used with the input-form
  form.setAttribute('data-block-selection-type', comment.content.selection_type)
  form.setAttribute('data-block-comment-id', comment.id)

  form.querySelector('#selection_type').value = comment.content.selection_type
  form.querySelector('#block_id').value = comment.content.block_id

  // we use setAttribute to force adding the value, else
  // for some reasons it would not work
  form.querySelector('#author').setAttribute('value', comment.content.user)

  createComment(form, article_comment, comment)
}

function blockFocus() {
  // block-focus overlay

  const blocks = Array.from(document.querySelectorAll('.block'))
        .filter(block => !block.classList.contains('columns'))

  const focusOverlay = document.querySelector('.focus-overlay')
  
  blocks.forEach(block => {
    block.addEventListener('focus', (e) => {
      e.target.classList.add('selected')
      focusOverlay.classList.toggle('active')
    })

    block.addEventListener('blur', (e) => {
      e.target.classList.remove('selected')
      focusOverlay.classList.toggle('active')
    })
  })
}

function comments(article_slug) {
  const user_store = new LocalStore('user')
  const user = user_store.getByID(article_slug)

  const comment_store = new LocalStore(`comment-${article_slug}`)
  const comments = comment_store.getAll()
  const comment_forms = document.querySelectorAll( '.comment_form' )

  for (const comment_form of comment_forms) {
    // -- set comment to default username if there's any
    if (user !== undefined) {
      comment_form.querySelector('#author').value = user.value
    }

    // -- setup article's draft comments
    //    check if comment has `status: draft` 
    //    and matches current block-id
    const blockID = comment_form.dataset.blockId
    const comments_draft = comments.filter(comment => {
      if (comment.status === 'draft' && comment.content.block_id === blockID) {
        return comment
      }
    })

    if (comments_draft.length > 0) {
      comments_draft.map(comment => {
        commentsArticle(comment, comment_form)
      })
    }

    comment_form.onsubmit = respond_comment
  }

}

module.exports = { respond_comment,
                   commentsArticle,
                   blockFocus,
                   comments }
