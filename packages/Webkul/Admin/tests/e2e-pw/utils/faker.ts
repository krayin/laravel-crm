import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const usedNames = new Set();
const usedEmails = new Set();
const usedNumbers = new Set();
const usedSlugs = new Set();

/**
 * Generate a random name.
 */
function generateName() {
    const adjectives = [
        "Cool", "Smart", "Fast", "Sleek", "Innovative", "Shiny", "Bold", 
        "Elegant", "Epic", "Mystic", "Brilliant", "Luminous", "Radiant", 
        "Majestic", "Vivid", "Glowing", "Dynamic", "Fearless", "Silent", 
        "Electric", "Golden", "Blazing", "Timeless", "Noble", "Eternal"
      ];
      
      const nouns = [
        "Star", "Vision", "Echo", "Spark", "Horizon", "Nova", "Shadow", 
        "Wave", "Pulse", "Vortex", "Zenith", "Element", "Flare", "Comet", 
        "Galaxy", "Ember", "Crystal", "Sky", "Stone", "Blaze", "Eclipse", 
        "Storm", "Orbit", "Phantom", "Mirage"
      ];

    let name = "";

    do {
        const adj = adjectives[Math.floor(Math.random() * adjectives.length)];
        const noun = nouns[Math.floor(Math.random() * nouns.length)];

        name = `${adj} ${noun}`;
    } while (usedNames.has(name));

    usedNames.add(name);

    return name;
}

/**
 * Generate the first Name.
 */
function generateFirstName() {
    const firstNames = [
        "James",
        "Emma",
        "Liam",
        "Olivia",
        "Noah",
        "Ava",
        "William",
        "Sophia",
        "Benjamin",
        "Isabella",
        "Lucas",
        "Mia",
    ];

    return firstNames[Math.floor(Math.random() * firstNames.length)];
}

/**
 * Generate the last name.
 */
function generateLastName() {
    const lastNames = [
        "Smith",
        "Johnson",
        "Brown",
        "Williams",
        "Jones",
        "Garcia",
        "Miller",
        "Davis",
        "Rodriguez",
        "Martinez",
        "Hernandez",
        "Lopez",
    ];

    return lastNames[Math.floor(Math.random() * lastNames.length)];
}

/**
 * Generate the full name.
 */
function generateFullName() {
    return `${generateFirstName()} ${generateLastName()}`;
}

/**
 * Generate the email address.
 */
function generateEmail() {
    const adjectives = [
        "Cool",
        "Smart",
        "Fast",
        "Sleek",
        "Innovative",
        "Shiny",
        "Bold",
        "Elegant",
        "Epic",
        "Mystic",
        "Brilliant",
        "Luminous",
    ];

    const nouns = [
        "Star",
        "Vision",
        "Echo",
        "Spark",
        "Horizon",
        "Nova",
        "Shadow",
        "Wave",
        "Pulse",
        "Vortex",
        "Zenith",
        "Element",
    ];

    let email = "";

    do {
        const adj = adjectives[Math.floor(Math.random() * adjectives.length)];
        const noun = nouns[Math.floor(Math.random() * nouns.length)];
        const number = Math.floor(1000 + Math.random() * 9000);

        email = `${adj}${noun}${number}@example.com`.toLowerCase();
    } while (usedEmails.has(email));

    usedEmails.add(email);

    return email;
}

/**
 * Generate the phone number.
 */
function generatePhoneNumber() {
    let phoneNumber;

    do {
        phoneNumber = Math.floor(6000000000 + Math.random() * 4000000000);
    } while (usedNumbers.has(phoneNumber));

    usedNumbers.add(phoneNumber);

    return `${phoneNumber}`;
}

/**
 * Generate a random SKU.
 */
function generateSKU() {
    const letters = Array.from({ length: 3 }, () =>
        String.fromCharCode(65 + Math.floor(Math.random() * 26))
    ).join("");

    const numbers = Math.floor(1000 + Math.random() * 9000);

    return `${letters}${numbers}`;
}

/**
 * Generate a random URL.
 */
function generateSlug(delimiter = "-") {
    let slug;

    do {
        const name = generateName();

        const randomStr = Math.random().toString(36).substring(2, 8);

        slug = `${name
            .toLowerCase()
            .replace(/\s+/g, delimiter)}${delimiter}${randomStr}`;
    } while (usedSlugs.has(slug));

    usedSlugs.add(slug);

    return slug;
}

/**
 * Generate a random email subject.
 */
function generateEmailSubject() {
    const subjects = [
        "Exciting news just for you!",
        "Don't miss out on this opportunity!",
        "Limited time offer – act now!",
        "An exclusive deal awaits you!",
        "Your next big opportunity is here!",
        "Something special just for you!",
        "Unlock amazing benefits today!",
        "Surprise! A special gift inside!",
        "This could change everything for you!",
        "You're invited to something amazing!",
        "Get ready for a game-changing experience!",
        "Hurry! This offer won't last long!",
        "A deal you simply can’t resist!",
        "Exclusive access – only for you!",
        "We’ve got something exciting for you!",
        "Your perfect opportunity is here!",
        "Important update – check this out!",
        "Discover what’s waiting for you!",
        "A limited-time surprise for you!",
        "Special invitation – don’t miss out!",
    ];

    return subjects[Math.floor(Math.random() * subjects.length)];
}


/**
 * Generate the description.
 */
function generateDescription(length = 255) {
    const phrases = [
        "An innovative and sleek design.",
        "Built for speed and efficiency.",
        "Experience the future today.",
        "A perfect blend of style and power.",
        "Engineered to perfection.",
        "Designed for those who dream big.",
        "Unleash creativity with this masterpiece.",
        "A game-changer in every way.",
        "Smart, fast, and reliable.",
        "The perfect companion for your journey.",
        "Crafted with precision and excellence.",
        "Innovation that redefines possibilities.",
        "Enhancing your experience like never before.",
        "Where technology meets elegance.",
        "Power, performance, and perfection combined.",
        "Redefining the way you experience the world.",
        "A masterpiece of engineering and design.",
        "Unmatched quality and exceptional performance.",
        "Designed to elevate your lifestyle.",
        "Beyond expectations, beyond limits.",
    ];

    let description = "";

    while (length > 0) {
        let phrase = phrases[Math.floor(Math.random() * phrases.length)];

        if (phrase.length <= length) {
            description += (description ? " " : "") + phrase;
            length -= phrase.length;
        } else {
            description += " " + phrase.substring(0, length);
            break;
        }
    }

    return description.trim();
}

/**
 * Generate the host name.
 */
function generateHostname() {
    const words = [
        "tech",
        "cloud",
        "byte",
        "stream",
        "nexus",
        "core",
        "pulse",
        "data",
        "sync",
        "wave",
        "hub",
        "zone",
    ];

    const domains = [".com", ".net", ".io", ".ai", ".xyz", ".co"];

    const part1 = words[Math.floor(Math.random() * words.length)];
    const part2 = words[Math.floor(Math.random() * words.length)];
    const domain = domains[Math.floor(Math.random() * domains.length)];

    return `https://${part1}${part2}${domain}`;
}

/**
 * Generate a random element from the array.
 */
function randomElement(array) {
    return array[Math.floor(Math.random() * array.length)];
}

/**
 * Get a random image file from the directory.
 */
function getImageFile(
    directory = path.resolve(__dirname, "../data/images/")
) {
    if (!fs.existsSync(directory)) {
        throw new Error(`Directory does not exist: ${directory}`);
    }

    const files = fs.readdirSync(directory);
    const imageFiles = files.filter((file) =>
        /\.(gif|jpeg|jpg|png|svg|webp)$/i.test(file)
    );

    if (!imageFiles.length) {
        throw new Error("No image files found in the directory.");
    }

    const randomIndex = Math.floor(Math.random() * imageFiles.length);

    return path.join(directory, imageFiles[randomIndex]);
}

/**
 * Generate a random date from today onwards in YYYY-MM-DD format.
 */
function generateDate(): string {
    const today = new Date().getTime();
    const futureEnd = new Date(2030, 11, 31).getTime();
    const randomDate = new Date(today + Math.random() * (futureEnd - today));

    return randomDate.toISOString().split('T')[0];
}

/**
 * Function to generate a random company name
 */
function generateCompanyName() {
    const prefixes = [
        "Tech", "Software", "Innovate", "NextGen", "Cloud", "AI", "Cyber", "Digital",
        "Technical", "Product", "Organization", "Vendor", "Rock-on", "Super", "Quantum",
        "Neural", "Hyper", "Ultra", "Smart", "Future", "Mega", "Omni", "Virtual", "Dynamic",
        "Secure", "Data", "Meta", "Nano", "Robo", "Infinity", "Vision", "Intelli", "Strato",
        "Blue", "Green", "Red", "White", "Black", "Deep", "Elite", "Prime", "Titan", "Nova",
        "Storm", "Lightning", "Vertex", "Pioneer", "Omnis", "Synergy", "Core", "Nexus"
    ];
    const suffixes = [
        "Solutions", "Systems", "Pvt Ltd", "Technologies", "Enterprises", "Labs", "Networks",
        "Corporation", "Group", "Ventures", "Holdings", "Consulting", "Industries", "Analytics",
        "Innovations", "Services", "Softwares", "Developers", "AI", "Cloud", "Security", "Dynamics",
        "Technica", "Data", "Infotech", "Research", "Automation", "Synergy", "Strategies", "Platform",
        "Operations", "Logistics", "Infrastructure", "Management", "Digital", "Interactive",
        "Vision", "Connect", "Smart", "Solutions Inc", "Partners", "Tech Ltd", "Info Systems",
        "Growth", "Intelligence", "RoboCorp", "Edge", "Enterprise", "Global", "Power", "NextGen",
        "Creative"
    ];
    return `${prefixes[Math.floor(Math.random() * prefixes.length)]} ${suffixes[Math.floor(Math.random() * suffixes.length)]}`;
}

/**
 * Function to automate organization creation
 */
async function createOrganization(page) {
    const companyName = generateCompanyName();

    /**
     * Click on "Create Organization" button
     */
    await page.goto('admin/contacts/organizations');
    await page.getByRole('link', { name: 'Create Organization' }).click();

    /**
     * Fill in organization details
     */
    await page.getByRole('textbox', { name: 'Name *' }).fill(companyName);
    await page.locator('textarea[name="address\\[address\\]"]').fill('ARV Park');
    await page.getByRole('combobox').selectOption('IN');
    await page.locator('select[name="address\\[state\\]"]').selectOption('DL');
    await page.getByRole('textbox', { name: 'City' }).fill('Delhi');
    await page.getByRole('textbox', { name: 'Postcode' }).fill('123456');

    /** 
     * Click to add extra details
     */
    await page.locator('div').filter({ hasText: /^Click to add$/ }).nth(2).click();
    await page.getByRole('textbox', { name: 'Search...' }).fill('exampl');
    await page.getByRole('listitem').filter({ hasText: 'Example' }).click();

    /** 
     * Click on "Save Organization"
     */
    await page.getByRole('button', { name: 'Save Organization' }).click();
    // await expect(page.getByText(companyName)).toBeVisible();
    return companyName;
}

function generateJobProfile() {
    const jobProfiles = [
        "Playwright Automation Tester",
        "Software Engineer",
        "Data Analyst",
        "Project Manager",
        "DevOps Engineer",
        "QA Engineer",
        "UI/UX Designer",
        "Product Manager",
        "Cybersecurity Analyst",
        "Cloud Architect"
    ];
    const randomIndex = Math.floor(Math.random() * jobProfiles.length);
    return jobProfiles[randomIndex];
}

async function createPerson(page) {
    const Name = generateFullName();
    const email = generateEmail();
    const phone = generatePhoneNumber();
    const Job = generateJobProfile();

    await page.getByRole('link', { name: 'Create Person' }).click();

    await page.getByRole('textbox', { name: 'Name *' }).fill(Name);
    await page.getByRole('textbox', { name: 'Emails *' }).fill(email);
    await page.getByRole('textbox', { name: 'Contact Numbers' }).fill(phone);
    await page.getByRole('textbox', { name: 'Job Title' }).fill(Job);

    // Select an organization
    await page.locator('.relative > div > .relative').first().click();
    await page.getByRole('textbox', { name: 'Search...' }).fill('examp');
    await page.getByRole('listitem').filter({ hasText: 'Example' }).click();

    // Save person
    await page.getByRole('button', { name: 'Save Person' }).click();

    return { Name, email, phone };
}
function getRandomDateTime() {
    const year = Math.floor(Math.random() * (2030 - 2020 + 1)) + 2020;
    const month = String(Math.floor(Math.random() * 12) + 1).padStart(2, '0');
    const day = String(Math.floor(Math.random() * 28) + 1).padStart(2, '0');
    const hours = String(Math.floor(Math.random() * 24)).padStart(2, '0');
    const minutes = String(Math.floor(Math.random() * 60)).padStart(2, '0');
    const seconds = String(Math.floor(Math.random() * 60)).padStart(2, '0');
  
    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
  }

export {
    generateName,
    generateFirstName,
    generateLastName,
    generateFullName,
    generateEmail,
    generatePhoneNumber,
    generateSKU,
    generateSlug,
    generateEmailSubject,
    generateDescription,
    generateHostname,
    randomElement,
    getImageFile,
    generateDate,
    createOrganization,
    generateCompanyName,
    createPerson,
    getRandomDateTime
};