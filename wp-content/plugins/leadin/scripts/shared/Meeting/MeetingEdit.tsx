import React, { Fragment, useEffect } from 'react';
import { IMeetingBlockProps } from '../../gutenberg/MeetingsBlock/registerMeetingBlock';
import MeetingController from './MeetingController';
import PreviewMeeting from './PreviewMeeting';
import {
  BackgroudAppContext,
  useBackgroundAppContext,
  usePostBackgroundMessage,
} from '../../iframe/useBackgroundApp';
import { ProxyMessages } from '../../iframe/integratedMessages';
import LoadingBlock from '../Common/LoadingBlock';
import { useGetEmbedder } from '../../utils/useGetEmbedder';

interface IMeetingEditProps extends IMeetingBlockProps {
  preview?: boolean;
  origin?: 'gutenberg' | 'elementor';
  fullSiteEditor?: boolean;
}

function MeetingEdit({
  attributes: { url },
  isSelected,
  setAttributes,
  preview = true,
  origin = 'gutenberg',
  fullSiteEditor,
}: IMeetingEditProps) {
  const isBackgroundAppReady = useBackgroundAppContext();
  const monitorFormPreviewRender = usePostBackgroundMessage();

  const handleChange = (newUrl: string) => {
    setAttributes({
      url: newUrl,
    });
  };

  useEffect(() => {
    monitorFormPreviewRender({
      key: ProxyMessages.TrackMeetingPreviewRender,
      payload: {
        origin,
      },
    });
  }, [origin]);

  return !isBackgroundAppReady ? (
    <LoadingBlock />
  ) : (
    <Fragment>
      {(isSelected || !url) && (
        <MeetingController url={url} handleChange={handleChange} />
      )}
      {preview && url && (
        <PreviewMeeting url={url} fullSiteEditor={fullSiteEditor} />
      )}
    </Fragment>
  );
}

export default function MeetingsEditContainer(props: IMeetingEditProps) {
  const { embedder, errorElement, isLoading } = useGetEmbedder();

  if (errorElement) {
    return errorElement;
  }

  if (isLoading) {
    return <LoadingBlock />;
  }

  return (
    <BackgroudAppContext.Provider value={embedder}>
      <MeetingEdit {...props} />
    </BackgroudAppContext.Provider>
  );
}
