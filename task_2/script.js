document.addEventListener('DOMContentLoaded', function () {
  const selector = document.querySelector('[name="type_val"]');
  const paragraphs = document.querySelectorAll('p');

  function updatePage() {
    const selectedValue = selector.value;

    paragraphs.forEach(paragraph => {
      const input = paragraph.querySelector('input, select');
      if (input && (input.name.includes(selectedValue) || input === selector)) {
        paragraph.style.display = '';
      } else {
        paragraph.style.display = 'none';
      }
    });
  }

  updatePage();
  selector.addEventListener('change', updatePage);
});
