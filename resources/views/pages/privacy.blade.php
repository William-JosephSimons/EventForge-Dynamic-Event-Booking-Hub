<x-guest-layout>
    <h2 class="text-center font-semibold p-6" style='font-size: 20px;'>Privacy Policy and Terms of Use</h2>

    <p><strong>Last Updated:</strong> {{ now()->format('M j, Y') }}</p>

    <!-- Point 1: What data is collected -->
    <h3>1. What Data We Collect</h3>
    <p>To provide our event management services, we collect the following personal information upon registration:
    </p>
    <ul>
        <li><strong>Name:</strong> To identify you within the system.</li>
        <li><strong>Email Address:</strong> Used for account authentication, communication, and notifications.</li>
        <li><strong>Password:</strong> To secure your account.</li>
        <li><strong>Booking History:</strong> A record of the events you book.</li>
    </ul>

    <!-- Point 2: Why it is collected -->
    <h3>2. Why Your Data Is Collected</h3>
    <p>Your data is used exclusively for the following purposes:</p>
    <ul>
        <li>
            <strong>Authentication:</strong> To verify your identity and grant you secure access to the platform.
        </li>
        <li>
            <strong>Event Participation:</strong> To allow you to book events and for organisers to manage attendee
            lists.
        </li>
    </ul>

    <!-- Point 3: How it is stored and protected -->
    <h3>3. How Your Data Is Stored and Protected</h3>
    <p>We are committed to protecting your data. We implement the following security measures:</p>
    <ul>
        <li>
            <strong>Hashed Passwords:</strong> Your password is never stored in plain text. We use a strong, one-way
            hashing algorithm to protect it.
        </li>
        <li>
            <strong>Access Control:</strong> Our application has strict authorization rules to ensure users can only
            access the data they are permitted to see.
        </li>
    </ul>

    <!-- Point 4: User Rights -->
    <h3>4. Your Rights Over Your Data</h3>
    <p>As a user, you have the right to:</p>
    <ul>
        <li>
            <strong>View:</strong> You can view your name, email, and booking history within your account profile at
            any time.
        </li>
        <li><strong>Manage:</strong> You can update your name and password.</li>
        <li><strong>Delete:</strong> You have the right to delete your account and all associated personal data.
        </li>
    </ul>

    <p><strong>
            By clicking "I agree" during registration, you acknowledge that you have read, understood, and agree to be
            bound by this Privacy Policy and our Terms of Use.
        </strong></p>
</x-guest-layout>