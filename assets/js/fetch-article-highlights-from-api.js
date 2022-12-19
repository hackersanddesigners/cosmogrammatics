const textHighlightMergeUnique = require('./text-highlights-merge-unique')

async function fetchArticleHighlightsFromAPI(article_slug) {
  const csrf = document.querySelector('.toolbar > form').getAttribute('data-csrf')
 
  const url = `/api/cosmo/${ article_slug }`
  return fetch(url, {
    method: "GET",
    headers: {
      "X-CSRF" : csrf
    }
  })
  .then(response => response.json())
  .then(response => {
    const newData = response
    textHighlightMergeUnique(newData)

  })
  .catch(error => {
    return []
  });

}

module.exports = fetchArticleHighlightsFromAPI
