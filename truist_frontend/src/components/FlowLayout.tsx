import React from 'react';
import Header from './Header';
import Footer from './Footer';

interface FlowLayoutProps {
  children: React.ReactNode;
  variant?: 'default' | 'truist';
}

const FlowLayout: React.FC<FlowLayoutProps> = ({ children, variant = 'default' }) => {
  if (variant === 'truist') {
    return (
      <div className="min-h-screen flex flex-col bg-[#f6f3fa]">
        <header className="bg-[#2b0d49] text-white shadow-[0_2px_8px_rgba(22,9,40,0.45)]">
          <div className="max-w-6xl mx-auto h-16 flex items-center justify-start px-6">
            <img
              src="/assets/trulogo_horz-white.png"
              alt="Truist logo"
              className="h-8 w-auto"
            />
          </div>
        </header>

        <main className="flex-1 flex items-start justify-center px-4 sm:px-6 py-10">
          <div className="w-full max-w-5xl">{children}</div>
        </main>

        <div className="bg-white border-t border-[#d7cde2] shadow-[0_-2px_8px_rgba(22,9,40,0.05)]">
          <div className="max-w-6xl mx-auto px-6 py-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <button
              type="button"
              className="inline-flex items-center gap-2 text-sm text-[#2b0d49] border border-[#cabde0] rounded-full px-4 py-2"
            >
              <span>Disclosures</span>
              <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                <path strokeLinecap="round" strokeLinejoin="round" d="M6 9l6 6 6-6" />
              </svg>
            </button>
          </div>
        </div>

        <footer className="bg-[#2b0d49] text-white">
          <div className="max-w-6xl mx-auto px-6 py-8 grid gap-6 lg:grid-cols-[auto_1fr_auto] items-start">
            <div className="flex flex-col gap-3">
              <div className="flex items-center">
                <img
                  src="/assets/trulogo_horz-white.png"
                  alt="Truist logo"
                  className="h-8 w-auto"
                />
              </div>
              <p className="text-xs text-white/70 max-w-xs">
                Tailored banking experiences, demonstrated for students and simulation exercises only.
              </p>
            </div>

            <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 text-sm">
              {[
                ['Privacy', 'Accessibility'],
                ['Fraud & security', 'Limit use of my sensitive personal information'],
                ['Terms and conditions', 'Disclosures'],
              ].map((group, index) => (
                <ul key={index} className="space-y-2">
                  {group.map((item) => (
                    <li key={item}>
                      <a className="text-white/80 hover:text-white" href="#">
                        {item}
                      </a>
                    </li>
                  ))}
                </ul>
              ))}
            </div>
          </div>
          <div className="bg-black/90">
            <p className="max-w-6xl mx-auto px-6 py-3 text-center text-xs text-white/70">
              © 2025, Truist. All rights reserved.
            </p>
          </div>
        </footer>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-b from-[#f7f9fd] via-[#d6e0ec] to-[#4d6f96] flex flex-col">
      <Header />
      <main className="flex-1 flex items-center justify-center px-4 py-12">{children}</main>
      <Footer />
    </div>
  );
};

export default FlowLayout;
