#!/usr/bin/env python3
"""
Simple HTTP server to serve the toy sale test files publicly
"""
import http.server
import socketserver
import os
from urllib.parse import urlparse, parse_qs

class CustomHTTPRequestHandler(http.server.SimpleHTTPRequestHandler):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, directory="/workspace/project/toy-sale", **kwargs)
    
    def end_headers(self):
        # Add CORS headers
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', '*')
        super().end_headers()
    
    def do_GET(self):
        # Custom routing for test files
        if self.path == '/':
            self.send_response(200)
            self.send_header('Content-type', 'text/html')
            self.end_headers()
            
            html = """
<!DOCTYPE html>
<html>
<head>
    <title>Toy Sale Print Layout Tests</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .container { max-width: 800px; margin: 0 auto; }
        .test-link { 
            display: block; 
            padding: 15px; 
            margin: 10px 0; 
            background: #f0f0f0; 
            text-decoration: none; 
            color: #333;
            border-radius: 5px;
        }
        .test-link:hover { background: #e0e0e0; }
        .description { color: #666; font-size: 14px; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧸 Toy Sale Print Layout Tests</h1>
        <p>Click on any test below to view the print layout optimization for different toy quantities:</p>
        
        <a href="/tests/test_print_layout_10_toys.html" class="test-link">
            <strong>📄 Test with 10 Toys</strong>
            <div class="description">Should fit on 1 page - Perfect for small deposits</div>
        </a>
        
        <a href="/tests/test_print_layout_50_toys.html" class="test-link">
            <strong>📄 Test with 50 Toys</strong>
            <div class="description">Should span 2-3 pages logically - Medium deposits</div>
        </a>
        
        <a href="/tests/test_print_layout_100_toys.html" class="test-link">
            <strong>📄 Test with 100 Toys</strong>
            <div class="description">Should span 3-4 pages logically - Large deposits</div>
        </a>
        
        <hr style="margin: 30px 0;">
        
        <h2>🖨️ How to Test Print Layout</h2>
        <ol>
            <li>Click on any test link above</li>
            <li>Use your browser's <strong>Print Preview</strong> (Ctrl+P or Cmd+P)</li>
            <li>Verify the following improvements:</li>
        </ol>
        
        <h3>✅ Expected Results:</h3>
        <ul>
            <li><strong>No orphaned headers</strong> - Headers always appear with toy content</li>
            <li><strong>No orphaned footers</strong> - Footers always appear with summary + some toys</li>
            <li><strong>No split table rows</strong> - Individual toy entries are never split across pages</li>
            <li><strong>No blank pages</strong> - Optimal space utilization on A4 format</li>
            <li><strong>Proper content grouping</strong> - Related content stays together</li>
        </ul>
        
        <h3>🔧 Technical Implementation:</h3>
        <ul>
            <li><strong>CSS @media print rules</strong> - 245+ lines of print-specific styling</li>
            <li><strong>JavaScript optimization</strong> - Content analysis and dynamic grouping</li>
            <li><strong>HTML structure</strong> - Proper table sections and print classes</li>
            <li><strong>A4 format optimization</strong> - Margins and spacing for professional printing</li>
        </ul>
        
        <p style="margin-top: 30px; padding: 15px; background: #e8f4fd; border-radius: 5px;">
            <strong>💡 Tip:</strong> These tests demonstrate the print layout fixes implemented for issue #6. 
            The actual depot_jouet.php file has been updated with the same optimizations.
        </p>
    </div>
</body>
</html>
            """
            self.wfile.write(html.encode())
            return
        
        # Serve other files normally
        super().do_GET()

if __name__ == "__main__":
    PORT = 12000
    
    with socketserver.TCPServer(("0.0.0.0", PORT), CustomHTTPRequestHandler) as httpd:
        print(f"🚀 Toy Sale Print Test Server running on port {PORT}")
        print(f"📱 Access via: https://work-1-knzykozwcqfrfubc.prod-runtime.all-hands.dev")
        print(f"🖨️ Test the print layout improvements for issue #6")
        print(f"⏹️  Press Ctrl+C to stop the server")
        
        try:
            httpd.serve_forever()
        except KeyboardInterrupt:
            print("\n🛑 Server stopped")