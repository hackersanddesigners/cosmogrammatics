const LocalStore = require('./local.store')
const store = new LocalStore()

window.commenting_state = {
  commenting : false,
  comments   : {},
  author     : undefined,
}

// called on comment form "submit" action
function respond_comment( e ) {
  e.preventDefault()

  // get form from event and make comment from it
  const form = e.target
  const comment = make_comment( form, store )

  // save comment in array to batch post later
  save_comment( comment )

  // form: reset and hide form
  form.reset()
  form.blur()

  // create newly posted comment
  const article = make_comment_el( comment )

  // append new comment to comment thread
  // before <form> (blue circle)

  const thread = form.parentNode
  thread.insertBefore(article, form)

  // update comment count
  update_own_comment_count()

  // set name of author in UI and comments if changed
  const submitted_name = comment.content.user
  const existing_name  = window.commenting_state.author
  if ( existing_name !== submitted_name ) {
    set_author_name( submitted_name )
  }

  // bring focus to the form to continue commenting
  form.firstElementChild.focus()

}



function update_own_comment_count() {
  const own_comment_count_els = document.querySelectorAll('.own_comment_count')
  Array.from( own_comment_count_els ).map( el => {
    el.innerHTML = Object.values( window.commenting_state.comments ).length
  })
}

function enable_commenting_state() {
  window.commenting_state.commenting = true
  window.onbeforeunload = e => { e.preventDefault() }
  const editor_toolbar = document.getElementById( 'editor_toolbar' )
  const post_button = editor_toolbar.lastElementChild
  editor_toolbar.classList.remove( 'hidden' )
  post_button.removeAttribute( 'disabled' )
  editor_toolbar.onsubmit = post_comments
}

function disable_commenting_state() {
  window.commenting_state.commenting = false
  window.onbeforeunload = null
  const editor_toolbar = document.getElementById( 'editor_toolbar' )
  const post_button = editor_toolbar.lastElementChild
  post_button.setAttribute( 'disabled', true )
  editor_toolbar.onsubmit = null
  update_own_comment_count()
  editor_toolbar.classList.add( 'hidden' )
}


function set_author_name( author ) {

  // set it in the global state variable
  window.commenting_state.author = author
  Object.values( window.commenting_state.comments ).map( comment => {
    comment.content.user = author
  })

  // set it for all the forms
  const comment_forms = document.querySelectorAll( '.comment_form' )
  for ( const comment_form of comment_forms ) {
    const author_input = comment_form.querySelector( 'input[name="author"]' )
    author_input.value = author
    author_input.setAttribute('tabindex', '-1')
  }

  // set it for all of your own comment elements
  const comment_els = document.querySelectorAll( 'aside article.mine' )
  for ( const comment_el of comment_els ) {
    const author_el = comment_el.querySelector( '.author' )
    author_el.innerHTML = author
  }

}

function save_comment( comment ) {
  window.commenting_state.comments[comment.slug] = comment
}

async function post_comments( e ) {
  e.preventDefault()
  for ( const comment of Object.values( window.commenting_state.comments ) ) {
    const response = await post_comment( comment )
  }
  window.commenting_state.comments = {}
  disable_commenting_state()
}

function post_comment( comment ) {
  const csrf = comment.csrf
  const article_slug = comment.content.article_slug
  const url  = `/api/pages/articles+${ article_slug }+comments`

  return fetch( `${ url }/children` , {
    method: "POST",
    headers: { "X-CSRF": csrf },
    body: JSON.stringify( comment )
  })
  .then(response => response.json())
  .then(response => {

    // set comment to be published
    return fetch( `${ url }+${ response.data.slug }/status`, {
      method: "PATCH",
      headers: { "X-CSRF": csrf },
      body: JSON.stringify( { status: 'listed' } )
    })
    .then(response => response.json())
    .then( response => response )
    .catch( error => console.error( error ) )

  })
    .then(response => response)
    .catch(error => console.error(error) )

}


function make_comment( form, store ) {
  const chilren           = Array.from( form.children )
  const csrf              = form.getAttribute( 'data-csrf' )
  const article_slug      = form.getAttribute( 'data-article-slug' )
  const block_id          = form.getAttribute( 'data-block-id' )
  const selection_type    = form.getAttribute( 'data-block-selection-type' )
  const selection_text_id = form.getAttribute( 'data-block-selection-text-id' )
  const selection_text    = store.getByID(selection_text_id)
  const author            = chilren.find( c => c.name == 'author' ).value
  const text              = chilren.find( c => c.name == 'body' ).value
  const ts                = new Date().toISOString()
  return {
    slug: `test-${ ts }`,
    title: '',
    template: 'comment',
    csrf: csrf,
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

function make_comment_thread_el( form, position ) {
  const thread = document.createElement( 'section' )
  thread.classList.add( 'thread' )
  thread.classList.add( 'new' )
  thread.appendChild( form )
  thread.style.setProperty( '--top', position.top + 'px' )
  thread.style.setProperty( '--left', position.left + 'px' )
  return thread
}

function make_comment_form_el( template_form, source_id, block_id, position ) {
  const form = template_form.cloneNode( true )
  form.setAttribute('data-block-selection-type', 'text')
  form.setAttribute('data-block-selection-text-id', source_id )
  form.setAttribute('data-block-id', block_id )
  form.onsubmit = respond_comment
  return form
}

function make_comment_el( data ) {

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
  timestamp.setAttribute('datetime', data.content.timestamp)
  date.append(timestamp)
  date.innerHTML = `On ${data.content.timestamp}`

  const user = document.createElement('p')
  user.innerHTML = `by <span class="author">${data.content.user}</span>`

  footer.append(date)
  footer.append(user)

  // -- append everything to <article>
  const article = document.createElement('article')
  article.classList.add( 'mine' )
  article.setAttribute('tabindex', '0')

  article.append(text_comment)
  article.append(footer)

  return article

}

module.exports = {
  enable_commenting_state,
  respond_comment,
  post_comment,
  make_comment_el,
  make_comment_thread_el,
  make_comment_form_el,

}
