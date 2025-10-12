#!/usr/bin/env python3
"""
AI Bot Detection Effectiveness Testing Script

This script tests the Django anti-bot mechanism by sending requests
with various user agents and analyzing the responses to verify
AI bot detection accuracy.
"""
import requests
import json
import time
from datetime import datetime
from collections import defaultdict

class BotDetectionTester:
    """Test AI bot detection patterns against various user agents"""

    def __init__(self, base_url="http://localhost:8000"):
        self.base_url = base_url
        self.results = {
            'ai_bots_blocked': 0,
            'ai_bots_passed': 0,
            'legitimate_passed': 0,
            'legitimate_blocked': 0,
            'total_tests': 0,
            'test_details': []
        }

    def test_user_agent(self, user_agent, expected_blocked=False, category="unknown"):
        """Test a single user agent"""
        headers = {'User-Agent': user_agent}

        try:
            # Test against the main endpoint (should trigger middleware)
            response = requests.get(f"{self.base_url}/", headers=headers, timeout=10)

            actual_blocked = response.status_code == 403
            test_passed = actual_blocked == expected_blocked

            result = {
                'user_agent': user_agent,
                'category': category,
                'expected_blocked': expected_blocked,
                'actual_blocked': actual_blocked,
                'status_code': response.status_code,
                'response_time': response.elapsed.total_seconds(),
                'test_passed': test_passed,
                'timestamp': datetime.now().isoformat()
            }

            self.results['test_details'].append(result)
            self.results['total_tests'] += 1

            if expected_blocked and actual_blocked:
                self.results['ai_bots_blocked'] += 1
            elif expected_blocked and not actual_blocked:
                self.results['ai_bots_passed'] += 1
            elif not expected_blocked and not actual_blocked:
                self.results['legitimate_passed'] += 1
            elif not expected_blocked and actual_blocked:
                self.results['legitimate_blocked'] += 1

            return result

        except requests.exceptions.RequestException as e:
            # Network error - treat as inconclusive
            result = {
                'user_agent': user_agent,
                'category': category,
                'expected_blocked': expected_blocked,
                'actual_blocked': None,
                'status_code': None,
                'response_time': None,
                'test_passed': False,
                'error': str(e),
                'timestamp': datetime.now().isoformat()
            }

            self.results['test_details'].append(result)
            self.results['total_tests'] += 1
            return result

    def run_comprehensive_test(self):
        """Run comprehensive AI bot detection tests"""

        print("🚀 Starting AI Bot Detection Effectiveness Test")
        print(f"Target: {self.base_url}")
        print("=" * 60)

        # Test AI Bots (should be blocked)
        ai_bots = [
            "GPTBot/1.0 (+https://openai.com/gptbot)",
            "ClaudeBot/1.0 (+claudebot@anthropic.com)",
            "PerplexityBot/1.0 (+https://docs.perplexity.ai/docs/perplexity-bot)",
            "ChatGPT-User/1.0",
            "OAI-SearchBot/1.0",
            "anthropic-ai/1.0",
            "SemrushBot/7~bl",
            "MJ12bot/v1.4.0",
            "Applebot/0.1 (+http://www.apple.com/go/applebot)",
            "Bytespider/1.0"
        ]

        print(f"🛡️  Testing {len(ai_bots)} AI Bots (should be BLOCKED)")
        print("-" * 50)

        for bot_ua in ai_bots:
            result = self.test_user_agent(bot_ua, expected_blocked=True, category="ai_bot")
            status = "✅ BLOCKED" if result['actual_blocked'] else "❌ PASSED"
            print(f"  {bot_ua[:50]:<50} → {status} ({result['response_time']:.3f}s)")

        # Test Legitimate User Agents (should pass through)
        legitimate_uas = [
            "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
            "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
            "Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1",
            "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:120.0) Gecko/20100101 Firefox/120.0",
            "PostmanRuntime/7.35.0",
            "python-requests/2.31.0",
            "curl/7.68.0"
        ]

        print(f"\n👤 Testing {len(legitimate_uas)} Legitimate User Agents (should PASS)")
        print("-" * 50)

        for ua in legitimate_uas:
            result = self.test_user_agent(ua, expected_blocked=False, category="legitimate")
            status = "✅ PASSED" if not result['actual_blocked'] else "❌ BLOCKED"
            print(f"  {ua[:50]:<50} → {status} ({result['response_time']:.3f}s)")

        # Test Edge Cases
        edge_cases = [
            "",  # Empty user agent
            " ",  # Whitespace only
            "bot",  # Generic bot
            "crawler",  # Generic crawler
            "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)",  # Googlebot
            "Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)",  # Bingbot
        ]

        print(f"\n🔍 Testing {len(edge_cases)} Edge Cases")
        print("-" * 50)

        for ua in edge_cases:
            result = self.test_user_agent(ua, expected_blocked=True, category="edge_case")
            status = "✅ BLOCKED" if result['actual_blocked'] else "❌ PASSED"
            print(f"  {ua[:50]:<50} → {status} ({result['response_time']:.3f}s)")

        self.print_summary()

    def print_summary(self):
        """Print test results summary"""
        print("\n" + "=" * 60)
        print("📊 AI BOT DETECTION TEST RESULTS")
        print("=" * 60)

        total_ai = self.results['ai_bots_blocked'] + self.results['ai_bots_passed']
        total_legitimate = self.results['legitimate_passed'] + self.results['legitimate_blocked']

        print(f"🎯 AI Bots Tested: {total_ai}")
        print(f"   ✅ Correctly Blocked: {self.results['ai_bots_blocked']} ({self.results['ai_bots_blocked']/total_ai*100:.1f}%)" if total_ai > 0 else "   ✅ Correctly Blocked: 0")
        print(f"   ❌ Incorrectly Passed: {self.results['ai_bots_passed']}")

        print(f"\n👤 Legitimate UAs Tested: {total_legitimate}")
        print(f"   ✅ Correctly Passed: {self.results['legitimate_passed']} ({self.results['legitimate_passed']/total_legitimate*100:.1f}%)" if total_legitimate > 0 else "   ✅ Correctly Passed: 0")
        print(f"   ❌ Incorrectly Blocked: {self.results['legitimate_blocked']}")

        # Calculate overall accuracy
        total_correct = (self.results['ai_bots_blocked'] + self.results['legitimate_passed'])
        accuracy = (total_correct / self.results['total_tests'] * 100) if self.results['total_tests'] > 0 else 0

        print(f"\n🎖️  Overall Accuracy: {total_correct}/{self.results['total_tests']} ({accuracy:.1f}%)")

        if accuracy >= 95:
            print("🌟 EXCELLENT: AI bot detection is highly effective!")
        elif accuracy >= 80:
            print("👍 GOOD: AI bot detection is working well")
        elif accuracy >= 60:
            print("⚠️  FAIR: Some adjustments may be needed")
        else:
            print("❌ POOR: Significant improvements needed")

        # Save results to file
        self.save_results()

    def save_results(self):
        """Save detailed test results to file"""
        filename = f"ai_bot_test_results_{datetime.now().strftime('%Y%m%d_%H%M%S')}.json"

        with open(filename, 'w') as f:
            json.dump(self.results, f, indent=2, default=str)

        print(f"\n💾 Detailed results saved to: {filename}")

def main():
    """Main test execution"""
    print("🤖 AI Bot Detection Effectiveness Test Suite")
    print("=" * 60)

    # Check if server is running
    try:
        response = requests.get("http://localhost:8000/", timeout=5)
        print(f"✅ Server is running: {response.status_code}")
    except requests.exceptions.RequestException:
        print("❌ Server is not running. Please start: python manage.py runserver")
        return

    # Run tests
    tester = BotDetectionTester()
    tester.run_comprehensive_test()

if __name__ == "__main__":
    main()
