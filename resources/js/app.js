import './bootstrap';
import 'preline'

document.addEventListener('livewire:navigated', () => {
    window.HSStaticMethods.autoInit(); // when navigated this will initialize all preline components whis requires javascript code
})
