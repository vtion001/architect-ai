<!DOCTYPE html>
<html>
<head>
    <title>Connection Successful</title>
</head>
<body style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; font-family: sans-serif;">
    <h2>Connected to {{ ucfirst($platform) }}!</h2>
    <p>You can now close this window.</p>
    <script>
        // Send success message to the main planner window
        if (window.opener) {
            window.opener.postMessage({ type: 'connected', platform: '{{ $platform }}' }, '*');
            setTimeout(() => {
                window.close();
            }, 1000);
        }
    </script>
</body>
</html>
