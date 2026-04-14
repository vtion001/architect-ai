export default {
  ci: {
    collect: {
      url: [
        'http://localhost:8081/dashboard',
        'http://localhost:8081/documents',
        'http://localhost:8081/research',
        'http://localhost:8081/content-creator',
      ],
      numberOfRuns: 3,
      settings: {
        preset: 'desktop',
        throttling: {
          rttMs: 40,
          throughputMbps: 10,
          cpuSlowdownMultiplier: 1,
        },
      },
    },
    assert: {
      preset: 'lighthouse:recommended',
      assertions: {
        'categories:performance': ['error', { minScore: 0.9 }],
        'largest-contentful-paint': ['error', { maxNumericValue: 2500 }],
        'cumulative-layout-shift': ['error', { maxNumericValue: 0.1 }],
        'first-input-delay': ['error', { maxNumericValue: 100 }],
        'first-contentful-paint': ['warn', { maxNumericValue: 1800 }],
        'speed-index': ['warn', { maxNumericValue: 3400 }],
        'total-blocking-time': ['error', { maxNumericValue: 300 }],
        'render-blocking-resources': ['warn', { maxLength: 0 }],
        'unused-javascript': ['warn', { maxLength: 0 }],
        'unused-css-rules': ['warn', { maxLength: 0 }],
        'uses-optimized-images': ['warn'],
        'uses-webp-images': ['warn'],
        'uses-text-compression': ['error'],
        'uses-responsive-images': ['warn'],
        'efficiently-encoded-images': ['warn'],
      },
    },
    upload: {
      target: 'filesystem',
      outputDir: './reports',
      reportFilename: 'lhci.%Y.%m.%d.%H.%M.html',
    },
  },
};
