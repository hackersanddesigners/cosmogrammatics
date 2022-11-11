
function respond_comment( e ) {
  e.preventDefault()

  const form    = e.target
  const chilren = Array.from( form.children )

  post_comment({
    csrf             : form.getAttribute( 'data-csrf' ),
    article_slug     : form.getAttribute( 'data-article-slug' ),
    block_id         : form.getAttribute( 'data-block-id' ),
    selection_type   : form.getAttribute( 'data-selection-type' ),
    selection_coords : form.getAttribute( 'data-selection-coords' ),
    author           : chilren.find( c => c.name == 'author' ).value,
    text             : chilren.find( c => c.name == 'body' ).value,
  })
    .then( response => {
      if (response.status === 'ok') {

        // -- form: reset and hide form
        form.reset()
        form.classList.remove('focus-in')

        // add newly posted comment into DOM
        const data = response.data

        // -- section
        const text_comment = document.createElement('section')
        const section_text = data.content.text
        text_comment.append(section_text)
        
        // -- footer
        const footer = document.createElement('footer')
        const date = document.createElement('p')
        const timestamp = document.createElement('time')
        // TODO set correct datetime format for timestamp `yyyy-mm-dd hh:mm:ss`
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

        
        // append new comment to comment thread
        // before <form> (blue circle)
        const aside_threads = document.querySelector('.thread')
        aside_threads.insertBefore(article, aside_threads.querySelector('form'))
        
      }
    })
}



function post_comment( comment ) {

  const {
    csrf,
    article_slug,
    block_id,
    selection_type,
    selection_coords,
    author,
    text,
  } = comment

  // const ts   = new Date().toISOString().split('.')[0]+"Z"
  const ts   = new Date().toISOString()
  // should be toLocaleTimeString? or should convert this in kirby?

  const url  = `/api/pages/articles+${ article_slug }+comments`
  const body = {
    slug: `test-${ ts }`,
    title: '',
    template: 'comment',
    content: {
      user: author,
      timestamp: ts,
      article_slug: article_slug,
      block_id: block_id,
      text: text,
      selection_type: selection_type,
      selection_coords: selection_coords
    }
  }

  return fetch( `${ url }/children` , {
    method: "POST",
    headers: { "X-CSRF": csrf },
    body: JSON.stringify( body )
  })
  .then(response => response.json())
  .then(response => {

    // set comment to be visible
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


export {
  respond_comment,
  post_comment
}
