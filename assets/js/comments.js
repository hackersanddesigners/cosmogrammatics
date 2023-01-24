const LocalStore = require('./local.store')
const store = new LocalStore()
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
  let comment = make_comment(form, store)
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
  
  const article_slug = comment.content.article_slug
  const comment_store = new LocalStore(`comment-${article_slug}`)
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

  const input_text = xss(input_body.value.trim())
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
  const selection_text_id = form.getAttribute( 'data-block-selection-text-id' )
  const selection_text    = store.getByID(selection_text_id)
  const author            = xss(children.find( c => c.name == 'author' ).value)
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

  // TODO set correct datetime format for timestamp `yyyy-mm-dd hh:mm:ss`
  const ts = new Date(data.content.timestamp)
  timestamp.setAttribute('datetime', data.content.timestamp)
  date.append(timestamp)
  date.innerHTML = `On ${data.content.timestamp}`

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
    console.log('block_id =>', [form, block_id])

    if (block_id !== '') {
      const block = document.getElementById(block_id)
      const aside = block.querySelector('aside')
      const thread_form = Array.from(thread.children)[0]
      
      thread.insertBefore(article, thread_form)
      aside.appendChild(thread)

      // hide input-form
      form_parent.classList.add('hidden')
      form_parent.style.removeProperty('--top')
      form_parent.style.removeProperty('--left')
    }

  } else {
    thread = form_parent
    thread.insertBefore(article, form)
  }

  updateCommentCounter('increase', 1)

}

function commentsArticle(comment, form) {
  const article_comment = make_comment_el(comment)

  // set missing data to be used with the input-form
  form.setAttribute('data-block-selection-type', comment.content.selection_type)
  form.setAttribute('data-block-selection-text-id', comment.id)

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

module.exports = { respond_comment,
                   commentsArticle,
                   blockFocus }
