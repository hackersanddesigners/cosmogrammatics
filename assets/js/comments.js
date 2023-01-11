const LocalStore = require('./local.store')
const store = new LocalStore()
const {commentReviewList} = require('./comment-review.js')
const xss = require('xss')


function respond_comment( e ) {
  e.preventDefault()

  const form    = e.target

  // -- save comment to localStorage under:
  //    <current-article-url>: [{..}, ...]

  // to correctly save object into LocalStore
  // the object needs to have an ID field
  // else LocalStore will replace the previous
  // comment with the newest one only
  let comment = make_comment( form, store )
  comment['id'] = comment.content.selection_text.id
  
  const article_slug = comment.content.article_slug
  const comment_store = new LocalStore(`comment-${article_slug}`)
  comment_store.save(comment)

  commentReviewList(article_slug)


  // ---
  // form: reset and hide form
  form.reset()
  form.blur()

  // create newly posted comment
  const article = make_comment_el( comment )

  // append new comment to comment thread
  // before <form> (blue circle)

  let thread

  // first check if the form is the toolbar form
  const form_parent = form.parentNode
  if ( form_parent.classList.contains( 'toolbar' ) ) {
    // make a new comment thread for this block
    thread = make_comment_thread_el( form )
    const block_id = form.getAttribute( 'data-block-id' )
    const block = document.getElementById( block_id )
    const aside = block.querySelector( 'aside' )
    const thread_form = Array.from( thread.children )[0]
    thread.insertBefore( article, thread_form )
    aside.appendChild( thread )
  } else {
    thread = form_parent
    thread.insertBefore(article, form)
  }

  // update comment count
  const comment_count = document.querySelector('#comment_count')
  comment_count.innerHTML = new Number( comment_count.innerHTML ) + 1

  article.focus()

}

function make_comment( form, store ) {
  const chilren           = Array.from( form.children )
  const article_slug      = form.getAttribute( 'data-article-slug' )
  const block_id          = form.getAttribute( 'data-block-id' )
  const selection_type    = form.getAttribute( 'data-block-selection-type' )
  const selection_text_id = form.getAttribute( 'data-block-selection-text-id' )
  const selection_text    = store.getByID(selection_text_id)
  const author            = xss(chilren.find( c => c.name == 'author' ).value)
  const text              = xss(chilren.find( c => c.name == 'body' ).value)
  const ts                = new Date().toISOString()

  return {
    slug: ts,
    title: ts,
    template: 'comment',
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

  article.append(text_comment)
  article.append(footer)

  return article

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


module.exports = {
  respond_comment,
  post_comment
}
