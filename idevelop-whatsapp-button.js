document.addEventListener('DOMContentLoaded', function() {
    const button = document.querySelector('.my-whatsapp-button');
    
    // Add text label if provided
    const options = myWhatsappButton; // Localized script data

    if (options && options.text_label) {
        const textLabel = document.createElement('span');
        textLabel.classList.add('my-whatsapp-button-label');
        textLabel.textContent = options.text_label;
        button.appendChild(textLabel);
    }
    if (!button) {
        return;
    }

    // Set target attribute based on option
    if (options.open_new_tab) {
        button.setAttribute('target', '_blank');
        button.setAttribute('rel', 'noopener noreferrer');
    } else {
        button.removeAttribute('target');
        button.removeAttribute('rel');
    }

    // Add call-to-action text as a data attribute
    if (options.call_to_action_text) {
        button.setAttribute('data-cta-text', options.call_to_action_text);
    }

    // Apply animation classes
    if (options.enable_pulse_animation) {
        button.classList.add('animate-pulse');
    }
    if (options.enable_fade_in_animation) {
        button.classList.add('animate-fade-in');
    }
 
    // Handle dynamic message content
    button.addEventListener('click', function(event) {
        let message = options.pre_filled_message || '';
        
        // Replace {{url}} with current page URL
        message = message.replace(/{{url}}/g, encodeURIComponent(window.location.href));
        
        // Replace {{title}} with current page title
        message = message.replace(/{{title}}/g, encodeURIComponent(document.title));

        // Replace custom field placeholders like {{field_name}}
        const customFieldRegex = /{{(.*?)}}/g;
        let match;
        while ((match = customFieldRegex.exec(message)) !== null) {
            const fieldName = match[1];
            const fieldElement = document.querySelector(`[data-whatsapp-field="${fieldName}"]`);
            if (fieldElement) {
                const fieldValue = encodeURIComponent(fieldElement.textContent.trim());
                message = message.replace(new RegExp(`{{${fieldName}}}`, 'g'), fieldValue);
            } else {
                // If field not found, remove the placeholder
                message = message.replace(new RegExp(`{{${fieldName}}}`, 'g'), '');
            }
        }

        // Construct the final WhatsApp URL
        let whatsappUrl = `https://wa.me/${options.phone}`;
        if (message) {
            whatsappUrl += `?text=${message}`;
        }
        
        button.setAttribute('href', whatsappUrl);
    });
 
    // Device type visibility
    const isMobile = /Mobi|Android/i.test(navigator.userAgent);
    const isTablet = /(tablet|ipad|playbook|silk)|(android(?!.*mobile))/i.test(navigator.userAgent);
    const isDesktop = !isMobile && !isTablet;

    let showOnDevice = false;
    if (options.display_devices.includes('desktop') && isDesktop) {
        showOnDevice = true;
    }
    if (options.display_devices.includes('mobile') && isMobile) {
        showOnDevice = true;
    }
    if (options.display_devices.includes('tablet') && isTablet) {
        showOnDevice = true;
    }

    if (!showOnDevice) {
        button.style.display = 'none';
        return;
    }

    // Initial state: hidden
    button.style.display = 'none';

    // Delay before appearance
    if (options.delay > 0) {
        setTimeout(() => {
            handleVisibility();
        }, options.delay * 1000);
    } else {
        handleVisibility();
    }

    function handleVisibility() {
        if (options.scroll_percentage > 0) {
            window.addEventListener('scroll', checkScroll);
            checkScroll(); // Check immediately in case page is already scrolled
        } else {
            button.style.display = 'flex'; // Show if no scroll percentage
            if (options.enable_fade_in_animation) {
                button.classList.add('animate-fade-in');
            }
        }
    }
 
    function checkScroll() {
        const scrollY = window.scrollY;
        const documentHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrolledPercentage = (scrollY / documentHeight) * 100;
 
        if (scrolledPercentage >= options.scroll_percentage) {
            button.style.display = 'flex';
            if (options.enable_fade_in_animation) {
                button.classList.add('animate-fade-in');
            }
            window.removeEventListener('scroll', checkScroll); // Remove listener once shown
        } else {
            button.style.display = 'none';
            if (options.enable_fade_in_animation) {
                button.classList.remove('animate-fade-in');
            }
        }
    }
});