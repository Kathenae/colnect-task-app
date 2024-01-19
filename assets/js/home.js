// DOM Query Selectors
const querySelectors = {
   targetElementInput: 'input[name="targetElement"]',
   targetUrlInput: 'input[name="targetUrl"]',
   submitButton: 'button[type="submit"]',
   requestResultsDiv: '#requestResults',
   requestStatsDiv: '#requestStats',
   urlErrorsUl: "#targetUrlErrors",
   elementErrorsUl: "#targetElementErrors",
   errorMessageDiv: "#errorMessage",
   resultsCardDiv: "#resultsCard"
};

// Event Handler for Form Submission
async function handleSubmit(event) {
   event.preventDefault();
   const validation = validateForm(event.target)

   if (validation.validated == false) {
      showInputErrors(validation.errors);
      return;
   }

   try {
      setLoading(true);
      clearErrors();

      const { targetUrl, targetElement } = validation.data
      const response = await requestPageAnalysis(targetUrl, targetElement);

      setLoading(false);

      if (response.status === STATUS_SUCCESS) {
         displayRequestResults(response.data);
         displayRequestStats(response.data);
         scrollToResults();
      }
      else {
         handleResponseErrors(response)
      }

   } catch (err) {
      console.error(err);
      hideRequestResults();
      hideRequestStats();
      showErrorMessage("Something went wrong while sending the request to server")
      setLoading(false);
   }
}

function validateForm(form) {
   const formData = new FormData(form);

   let targetUrl = formData.get('targetUrl');
   let targetElement = formData.get('targetElement');
   targetElement = targetElement.toLowerCase();

   const validation = {
      validated: true,
      errors: {
         targetUrl: [],
         targetElement: [],
      }
   }

   if (typeof targetUrl !== 'string') {
      validation.validated = false;
      validation.errors.targetUrl.push(`This Must be a valid string`);
   }
   else if (targetUrl.length <= 0) {
      validation.validated = false;
      validation.errors.targetUrl.push(`Please provide the URL of the page you wish to analyse`);
   }
   else if (!isValidUrl(targetUrl)) {
      validation.validated = false;
      validation.errors.targetUrl.push(`"${targetUrl}" is not a valid url`);
   }

   if (typeof targetElement !== 'string') {
      validation.validated = false;
      validation.errors.targetElement.push(`This Must be a valid string`);
   }
   else if (targetElement.length <= 0) {
      validation.validated = false;
      validation.errors.targetElement.push(`Please provide the name of the html element you wish to count`);
   }
   else if (!isValidHtmlElement(targetElement)) {
      validation.validated = false;
      validation.errors.targetElement.push(`"${targetElement}" is not a valid html element`);
   }

   if (validation.validated) {
      validation.data = {
         targetElement,
         targetUrl,
      };
   }

   return validation;
}

function isValidHtmlElement(name) {
   return HTML_ELEMENT_NAMES.includes(name);
}

function isValidUrl(url) {
   try {
      new URL(url);
      return true;
   } catch (error) {
      return false;
   }
}

// handles validation and error responses
function handleResponseErrors(response) {
   hideRequestResults();
   hideRequestStats();

   if (response.status === STATUS_VALIDATION_ERROR) {
      showInputErrors(response.errors);
   }

   if (response.status === STATUS_ERROR_MESSAGE) {
      showErrorMessage(response.message);
   }
}

// Set loading state and update UI
function setLoading(loading) {
   const progressEl = document.querySelector('#progress');
   progressEl.toggleAttribute('hidden', !loading);

   toggleElementState(querySelectors.requestResultsDiv, loading);
   toggleElementState(querySelectors.requestStatsDiv, loading);
   toggleElementState(querySelectors.submitButton, loading);

   toggleInputState(querySelectors.targetUrlInput, loading);
   toggleInputState(querySelectors.targetElementInput, loading);
}

// Toggle element visibility
function toggleElementState(selector, state) {
   const element = document.querySelector(selector);
   element.toggleAttribute('hidden', state);
}

// Toggle input disabled state
function toggleInputState(selector, state) {
   const input = document.querySelector(selector);
   input.toggleAttribute('disabled', state);
}

// Clear error messages
function clearErrors() {
   clearErrorList(querySelectors.elementErrorsUl);
   clearErrorList(querySelectors.urlErrorsUl);
   clearErrorMessage(querySelectors.errorMessageDiv);
}

// Clear error list
function clearErrorList(selector) {
   const errorList = document.querySelector(selector);
   errorList.innerHTML = '';
}

// Clear error message
function clearErrorMessage(selector) {
   const errorMessage = document.querySelector(selector);
   errorMessage.innerHTML = '';
}

// Show input errors
function showInputErrors(errors) {
   showErrorsOnList(querySelectors.elementErrorsUl, errors.targetElement);
   showErrorsOnList(querySelectors.urlErrorsUl, errors.targetUrl);
}

// Show errors on list
function showErrorsOnList(selector, errorMessages) {
   const errorsContainer = document.querySelector(selector);

   if (!errorMessages) {
      return;
   }

   errorsContainer.innerHTML = '';
   errorMessages.forEach((message) => {
      errorsContainer.innerHTML += `<li class="text-error">${message}</li>\n`;
   });
}

// Show error message
function showErrorMessage(message) {
   const errorMessage = document.querySelector(querySelectors.errorMessageDiv);
   errorMessage.innerHTML = `<h6 class="text-error">${message}</h6>`;
}

// Send request to server
async function requestPageAnalysis(targetUrl, targetElement) {
   const res = await fetch('/api/count-elements', {
      method: 'POST',
      body: JSON.stringify({ targetUrl, targetElement })
   });

   return res.json();
}

// Display request results
function displayRequestResults(data) {
   const resultsList = document.querySelector(`${querySelectors.requestResultsDiv} ul`);
   const { urlName, fetchedAt, fetchDurationMs, elementName, elementCount } = data;

   resultsList.innerHTML = `
     <li>URL <b>${urlName}</b> Fetched on ${new Date(fetchedAt.date).toLocaleString()}, took <b>${fetchDurationMs.toFixed(0)}ms.</b></li>
     <li>Element <mark>${elementName}</mark> appeared <b>${elementCount} times</b> in the page</li>
   `;
}

// Display request stats
function displayRequestStats(data) {
   const statsList = document.querySelector(`${querySelectors.requestStatsDiv} ul`);
   const { domainName, elementName, stats } = data;

   statsList.innerHTML = `
     <li>${stats.domainTotalUrls} different URLs from <b>${domainName}</b> have been fetched</li>
     <li>Average fetch time from <b>${domainName}</b> during the last 24 hours is <b>${stats.domainAvgResponseTime.toFixed(0)}ms</b></li>
     <li>There was a totalof <b>${stats.elementsCountOnDomain}</b> <mark>${elementName}</mark> elements counted from <b>${domainName}</b></li>
     <li>Total of <b>${stats.elementCountOnAllRequests}</b> <mark>${elementName}</mark> elements counted in all requests ever made.</li>
   `;
}

// Scroll to results
function scrollToResults() {
   const resultsCard = document.querySelector(querySelectors.resultsCardDiv);
   resultsCard.scrollIntoView({ behavior: "smooth" });
}

// Hide request results
function hideRequestResults() {
   toggleElementState(querySelectors.requestResultsDiv, true);
}

// Hide request stats
function hideRequestStats() {
   toggleElementState(querySelectors.requestStatsDiv, true);
}