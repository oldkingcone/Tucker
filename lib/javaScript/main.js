const frames = [
    "😂",
    "🤣",
    "😹",
    "(＾▽＾)",
    "(≧∇≦)",
    "(^ω^)",
    "😂🤣",
    "(＾▽＾)"
];

function fireAction(boxName, outputId) {
    const outputElement = document.getElementById(outputId);
    if (!outputElement) {
        console.error('Output element not found.');
        return;
    }
    outputElement.innerHTML = `<p class="info">Fetching data for ${boxName}...</p>`;
    let action = '';
    switch (boxName) {
        case 'Local Users':
            action = 'searchLocalUsers';
            break;
        case 'LDAP Users':
            // action = 'searchLDAPUsers';
            outputElement.innerHTML = `<p class="error">Error: Unknown action for ${boxName}. Check back in a bit, building it out.</p>`;
            break;
        case 'LOLBins':
            action = 'fetchLolBins';
            break;
        case 'LOLDrivers':
            action = 'fetchLolDrivers';
            break;
        case 'ServicePaths':
            action = 'fetchServicePaths';
            break;
        default:
            outputElement.innerHTML = `<p class="error">Error: Unknown action for ${boxName}.</p>`;
            return;
    }
    fetch('index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: action,
        }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                switch (boxName) {
                    case 'LDAP Users':
                    case 'Local Users':
                        outputElement.innerHTML = formatUserOutput(data.data);
                        applyUserEvents(outputId);
                        break;

                    case 'LOLBins':
                        outputElement.innerHTML = formatLOLBinsOutput(data.data);
                        applyLOLBinsEvents(outputId);
                        break;
                    case 'LOLDrivers':
                    case 'ServicePaths':
                    default:
                        console.error('No specific logic found for this boxName:', boxName);
                }
            } else {
                outputElement.innerHTML = `<p class="error">Error: ${data.message}</p>`;
            }
        })
        .catch(error => {
            outputElement.innerHTML = `<p class="error">An error occurred: ${error.message}</p>`;
        });
}

function formatUserOutput(data) {
    let formattedOutput = '<ul>';

    data.forEach(user => {
        const userInfo = JSON.stringify(user).replace(/'/g, "&#39;"); // Escape single quotes to prevent issues
        formattedOutput += `
            <li 
                class="user-entry" 
                data-info='${userInfo}'>
                <strong>${user.Name || 'Unknown User'}</strong>
            </li>`;
    });

    formattedOutput += '</ul>';
    return formattedOutput;
}
function formatLOLBinsOutput(data) {
    if (!Array.isArray(data.data)) {
        return `<p class="error">Error: Invalid data for LOLBins.</p>`;
    }
    let formattedOutput = '<ul>';
    data.data.forEach(path => {
        if (typeof path !== "string") {
            console.warn("Skipping invalid path:", path);
            return;
        }
        const filename = path.split('\\').pop();
        formattedOutput += `
            <li 
                class="bin-entry" 
                data-info='${path}'>
                ${filename}
            </li>`;
    });
    formattedOutput += '</ul>';
    return formattedOutput;
}

function applyLOLBinsEvents(outputId) {
    document
        .querySelectorAll(`#${outputId} .bin-entry`)
        .forEach(entryEl => {
            entryEl.addEventListener('mouseover', showBinaryDetails);
            entryEl.addEventListener('mouseout', hideBinaryDetails);
        });
}

function applyUserEvents(outputId) {
    document
        .querySelectorAll(`#${outputId} .user-entry`)
        .forEach(entryEl => {
            entryEl.addEventListener('mouseover', event => showUserDetails(event.currentTarget)); // Ensure `currentTarget` is passed
            entryEl.addEventListener('mouseout', hideUserDetails);
        });
}

function showUserDetails(targetElement) {
    const userInfo = JSON.parse(targetElement.getAttribute('data-info'));
    let modal = document.getElementById('user-details-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'user-details-modal';
        document.body.appendChild(modal);
    }
    modal.innerHTML = `
        <strong>Name:</strong> ${userInfo.Name || 'N/A'}<br>
        <strong>Full Name:</strong> ${userInfo.FullName || 'N/A'}<br>
        <strong>SID:</strong> ${userInfo.SID || 'N/A'}<br>
        <strong>Description:</strong> ${userInfo.Description || 'N/A'}<br>
        <strong>Disabled:</strong> ${userInfo.Disabled ? 'Yes' : 'No'}<br>
        <strong>Password Changeable:</strong> ${userInfo.PasswordChangeable ? 'Yes' : 'No'}<br>
        <strong>Password Expires:</strong> ${userInfo.PasswordExpires ? 'Yes' : 'No'}
    `;

    const rect = targetElement.getBoundingClientRect();
    modal.style.position = 'absolute';
    modal.style.left = `${rect.right + 10}px`;
    modal.style.top = `${rect.top + window.scrollY}px`;
    modal.style.background = '#333';
    modal.style.color = '#fff';
    modal.style.padding = '10px';
    modal.style.border = '1px solid #555';
    modal.style.borderRadius = '4px';
    modal.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.4)';
    modal.style.zIndex = '1000';
    modal.style.display = 'block';
}


function hideUserDetails() {
    const modal = document.getElementById('user-details-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function showBinaryDetails(event) {
    const fullPath = event.target.getAttribute('data-info');
    if (!fullPath) {
        return;
    }
    let modal = document.getElementById('binary-details-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'binary-details-modal';
        document.body.appendChild(modal);
    }
    modal.innerHTML = `
        <strong>Full Path:</strong> ${fullPath}
    `;
    const rect = event.target.getBoundingClientRect();
    modal.style.position = 'absolute';
    modal.style.left = `${rect.right + 10}px`;
    modal.style.top = `${rect.top + window.scrollY}px`;
    modal.style.background = '#333';
    modal.style.color = '#fff';
    modal.style.padding = '10px';
    modal.style.border = '1px solid #555';
    modal.style.borderRadius = '4px';
    modal.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.4)';
    modal.style.zIndex = '1000';
    modal.style.display = 'block';
}

function hideBinaryDetails() {
    const modal = document.getElementById('binary-details-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}


let scrollSpeed = 200;
let currentFrame = 0;
function scrollTitle() {
    currentTitle = frames[currentFrame];
    document.title = currentTitle;
    currentFrame = (currentFrame + 1) % frames.length;
    setTimeout(scrollTitle, scrollSpeed);
}
scrollTitle();

function tblhfaf() {
    document.addEventListener("DOMContentLoaded", function () {
        const currentUserElement = document.getElementById('currentUser');
        function getRandomColor() {
            const randomRed = Math.floor(Math.random() * 256); // Random value between 0-255
            const randomGreen = Math.floor(Math.random() * 256);
            const randomBlue = Math.floor(Math.random() * 256);
            return `rgb(${randomRed}, ${randomGreen}, ${randomBlue})`;
        }

        function changeColor() {
            currentUserElement.style.color = getRandomColor();
        }
        setInterval(changeColor, 3000);
        const footerContent = `<footer class="site-footer">
            <p>&copy; 2023 Tucker - Created by <a href="https://github.com/oldkingcone">Oldkingcone</a>. All Rights Reserved.</p>
      <p>This software is distributed as-is and for educational value.</p>
    </footer>`;

        const footerChecker = setInterval(() => {
            if (!document.querySelector(".site-footer")) {
                const footer = document.createElement("footer");
                footer.innerHTML = footerContent;
                footer.className = "site-footer";
                document.body.appendChild(footer);
            }
        }, 500);
    });

}
tblhfaf();

