
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
    form.reset()
    // andré queryselctor any .comment and replace fields, insert at article slug / nlovkid
  })
}



function post_comment( comment ) {

  const {
    csrf,
    article_slug,
    block_id,
    selection_type,
    selection_text,
    author,
    text,
  } = comment

  const ts   = new Date().toISOString().split('.')[0]+"Z"
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
      selection_text: selection_text,
      selection_image: {
        x1: '',
        y1: '',
        x2: '',
        y2: ''
      },
      selection_audio: {
        t1: '',
        t2: ''
      },
      selection_video: {
        x1: '',
        y1: '',
        t1: '',
        x2: '',
        y2: '',
        t2: ''
      }
    }
  }

  return fetch( `${ url }/children` , {
    method: "POST",
    headers: { "X-CSRF": csrf },
    body: JSON.stringify( body )
  })
  .then(response => response.json())
  .then(response => {
    fetch( `${ url }+${ response.data.slug }/status`, {
      method: "PATCH",
      headers: { "X-CSRF": csrf },
      body: JSON.stringify( { status: 'listed' } )
    })
    .then(response => response.json())
    .then( response => response )
    .catch( error => console.error( error ) )
  })
  .then(response => response )
  .catch(error => console.error(error) )


}



export {
  respond_comment,
  post_comment
}
