import React from 'react';
import Header from './Header';
import Footer from './Footer';

interface FlowPageLayoutProps {
  title: string;
  description?: string;
  eyebrow?: string;
  breadcrumbLabel?: string;
  children: React.ReactNode;
  contentClassName?: string;
  afterContent?: React.ReactNode;
}

const FlowPageLayout: React.FC<FlowPageLayoutProps> = ({
  title,
  description,
  eyebrow,
  breadcrumbLabel,
  children,
  contentClassName = '',
  afterContent,
}) => {
  return (
    <div className="min-h-screen bg-white text-[#0e2f56] flex flex-col">
      <Header />

      <main className="flex-1">
        <section className="bg-gradient-to-r from-[#002c5c] via-[#014a90] to-[#0073ba] py-12 px-4">
          <div className="max-w-5xl mx-auto space-y-8">
            <div className="flex items-center gap-2 text-sm text-white/85">
              <a href="#" className="text-white/70 hover:text-white">
                Home
              </a>
              <span className="text-white/60">›</span>
              <span className="font-semibold">{breadcrumbLabel ?? title}</span>
            </div>

            <div className="bg-gradient-to-b from-[#f0f6fb] to-[#dfeef9] shadow-2xl rounded-sm px-8 py-10">
              <div className="max-w-3xl space-y-6">
                <div className="space-y-3">
                  {eyebrow && (
                    <p className="text-[0.65rem] font-semibold uppercase tracking-[0.4em] text-[#0b5da7]">
                      {eyebrow}
                    </p>
                  )}
                  <h1 className="text-2xl font-semibold text-[#0e2f56]">{title}</h1>
                  {description && (
                    <p className="text-sm text-[#0e2f56]/85 leading-relaxed">{description}</p>
                  )}
                </div>

                <div className={`bg-white rounded-sm shadow-md px-8 py-8 ${contentClassName}`}>{children}</div>
              </div>
            </div>
          </div>
        </section>
      </main>

      {afterContent}

      <Footer />
    </div>
  );
};

export default FlowPageLayout;
