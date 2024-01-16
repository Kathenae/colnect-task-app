const QUERY_TARGET_ELEMENT_INPUT = 'input[name="targetElement"]';
const QUERY_TARGET_URL_INPUT = 'input[name="targetUrl"]';
const QUERY_SUBMIT_BUTTON = 'button[type="submit"]';
const QUERY_REQUEST_RESULTS_DIV = '#requestResults';
const QUERY_REQUEST_STATS_DIV = '#requestStats';
const QUERY_URL_ERRORS_UL = "#targetUrlErrors";
const QUERY_ELEMENT_ERRORS_UL = "#targetElementErrors";
const QUERY_ERROR_MESSAGE_DIV = "#errorMessage";

async function handleSubmit(event) {
   event.preventDefault();
   const formData = new FormData(event.target);
   const targetUrl = formData.get('targetUrl');
   const targetElement = formData.get('targetElement');

   try {
      setLoading(true);
      clearErrors();

      const res = await fetch('/api/count-elements', {
         method: 'POST',
         body: JSON.stringify({
            targetUrl,
            targetElement,
         })
      });

      const response = await res.json();

      setLoading(false)

      if (response.status == 'success') {
         const { url, fetchedAt, fetchDurationMs, element, count } = response.data;
         const resultsList = document.querySelector('#requestResults ul');
         const n = 345;
         resultsList.innerHTML = `
         <li>URL <b>${url}</b> Fetched on ${new Date(fetchedAt.date).toLocaleString()}, took <b>${fetchDurationMs.toFixed(0)}msec.</b></li>\n
         <li>Element <mark>${element}</mark> appeared <b>${count} times</b> in the page</li>\n
         `;
      }
      else {
         document.querySelector(QUERY_REQUEST_RESULTS_DIV).toggleAttribute('hidden', true);
         document.querySelector(QUERY_REQUEST_STATS_DIV).toggleAttribute('hidden', true);

         if (response.status == 'validation-error') {
            showInputErrors(response.errors);
         }

         if (response.status == 'error-message') {
            showErrorMessage(response.message);
         }
      }
   }
   catch (err) {
      console.error(err);
      setLoading(false)
   }
}

function setLoading(loading) {

   // Show/Hide progress
   const progressEl = document.querySelector('#progress');
   progressEl.toggleAttribute('hidden', !loading);

   // Hide/Show results and stats elements
   document.querySelector(QUERY_REQUEST_RESULTS_DIV).toggleAttribute('hidden', loading);
   document.querySelector(QUERY_REQUEST_STATS_DIV).toggleAttribute('hidden', loading);

   // Set button loading state
   const submitButton = document.querySelector(QUERY_SUBMIT_BUTTON);
   submitButton.toggleAttribute('disabled', loading);

   // Set inputs disabled state
   const urlInput = document.querySelector(QUERY_TARGET_URL_INPUT);
   const elementInput = document.querySelector(QUERY_TARGET_ELEMENT_INPUT);
   urlInput.toggleAttribute('disabled', loading);
   elementInput.toggleAttribute('disabled', loading);
}

function clearErrors() {
   document.querySelector(QUERY_ELEMENT_ERRORS_UL).innerHTML = "";
   document.querySelector(QUERY_URL_ERRORS_UL).innerHTML = "";
   document.querySelector(QUERY_ERROR_MESSAGE_DIV).innerHTML = "";
}

function showInputErrors(errors) {
   showErrorsOnList(QUERY_ELEMENT_ERRORS_UL, errors.targetElement);
   showErrorsOnList(QUERY_URL_ERRORS_UL, errors.targetUrl);
}

function showErrorsOnList(selectorQuery, errorMessages) {
   const errorsContainer = document.querySelector(selectorQuery);
   if (!errorMessages) {
      return;
   }

   errorsContainer.innerHTML = "";
   errorMessages.forEach((message) => {
      errorsContainer.innerHTML += `<li class="text-error">${message}</li>`
   });
}

function showErrorMessage(message) {
   document.querySelector(QUERY_ERROR_MESSAGE_DIV).innerHTML = `<h6 class="text-error">${message}</h6>`;
}