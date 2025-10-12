// components/MetaTags.js

import { useEffect } from 'react';

const MetaTags = ({title}) => {
  useEffect(() => {
    document.title = title;

    const link = document.querySelector("link[rel*='icon']");
    if (link) {
      link.href = 'https://ck-assets.imgix.net/assets/1.97.6/favicons/favicon-32x32.png?h=16&amp;w=16';
    }

    let metaViewport = document.querySelector('meta[name="viewport"]');
    if (!metaViewport) {
      metaViewport = document.createElement('meta');
      metaViewport.name = 'viewport';
      document.head.appendChild(metaViewport);
    }
    metaViewport.content = 'width=device-width, initial-scale=1.0';

    let metaCharset = document.querySelector('meta[charset]');
    if (!metaCharset) {
      metaCharset = document.createElement('meta');
      metaCharset.charset = 'UTF-8';
      document.head.appendChild(metaCharset);
    }

    let metaDescription = document.querySelector('meta[name="description"]');
    if (!metaDescription) {
      metaDescription = document.createElement('meta');
      metaDescription.name = 'description';
      metaDescription.content = 'Free Credit Score & Free Credit Reports With Monitoring | Credit Karma';
      document.head.appendChild(metaDescription);
    }

    return () => {
      // Cleanup if necessary
      // Optionally, remove any added meta tags if you want to clean up when the component is unmounted
    };
  }, []);

  return null; // This component does not render anything to the UI
};

export default MetaTags;
